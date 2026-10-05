<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Finance extends BaseController
{
    public function index(): string
    {
        $month = $this->request->getGet('month');
        if (! is_string($month) || ! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month)) {
            $month = date('Y-m');
        }
        $lastDay = date('Y-m-t', strtotime($month . '-01'));
        $db = $this->db();
        $entries = $db->table('finance_entries f')->select('f.*, b.booking_code')->join('bookings b', 'b.id = f.booking_id', 'left')
            ->where('f.entry_date >=', $month . '-01')->where('f.entry_date <=', $lastDay)->orderBy('f.entry_date', 'DESC')->orderBy('f.id', 'DESC')->get()->getResultArray();
        $totals = $db->table('finance_entries')->select('type, SUM(amount) AS total')->where('entry_date >=', $month . '-01')->where('entry_date <=', $lastDay)->groupBy('type')->get()->getResultArray();
        $summary = ['masuk' => 0, 'keluar' => 0];
        foreach ($totals as $total) {
            $summary[$total['type']] = (float) $total['total'];
        }

        return $this->render('finance/index', ['title' => 'Buku kas', 'entries' => $entries, 'summary' => $summary, 'month' => $month]);
    }

    public function new(): string
    {
        return $this->render('finance/form', ['title' => 'Catat transaksi', 'bookings' => $this->db()->table('bookings')->orderBy('event_date', 'DESC')->get()->getResultArray()]);
    }

    public function create(): RedirectResponse
    {
        if (! $this->validate([
            'entry_date' => 'required|valid_date[Y-m-d]',
            'type' => 'required|in_list[masuk,keluar]',
            'category' => 'required|min_length[2]|max_length[80]',
            'description' => 'required|min_length[2]|max_length[255]',
            'amount' => 'required|decimal|greater_than[0]',
            'booking_id' => 'permit_empty|is_natural_no_zero',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $bookingId = $this->request->getPost('booking_id');
        $db = $this->db();
        $amount = (float) $this->request->getPost('amount');
        $booking = null;
        if ($bookingId) {
            if ($this->request->getPost('type') !== 'masuk') {
                return redirect()->back()->withInput()->with('error', 'Transaksi yang ditautkan ke pesanan harus berupa pemasukan.');
            }
            $booking = $db->table('bookings')->where('id', (int) $bookingId)->where('status !=', 'dibatalkan')->get()->getRowArray();
            if ($booking === null) {
                return redirect()->back()->withInput()->with('error', 'Pesanan tidak ditemukan atau sudah dibatalkan.');
            }
            if ($amount > (float) $booking['total_amount'] - (float) $booking['paid_amount']) {
                return redirect()->back()->withInput()->with('error', 'Pembayaran melebihi sisa tagihan pesanan.');
            }
        }
        $db->transStart();
        $db->table('finance_entries')->insert([
            'entry_date' => $this->request->getPost('entry_date'),
            'type' => $this->request->getPost('type'),
            'category' => trim((string) $this->request->getPost('category')),
            'description' => trim((string) $this->request->getPost('description')),
            'amount' => $amount,
            'booking_id' => $bookingId ? (int) $bookingId : null,
            'created_by' => session('user_id'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        if ($booking !== null) {
            $db->table('bookings')->where('id', $booking['id'])->update([
                'paid_amount' => (float) $booking['paid_amount'] + $amount,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $db->transComplete();

        return redirect()->to('/finance')->with('message', 'Transaksi berhasil dicatat.');
    }

    public function delete(int $id): RedirectResponse
    {
        $entry = $this->db()->table('finance_entries')->where('id', $id)->get()->getRowArray();
        if ($entry === null || $entry['booking_id'] !== null) {
            return redirect()->to('/finance')->with('error', 'Transaksi pesanan tidak dapat dihapus dari buku kas.');
        }
        $this->db()->table('finance_entries')->where('id', $id)->delete();

        return redirect()->to('/finance')->with('message', 'Transaksi berhasil dihapus.');
    }
}
