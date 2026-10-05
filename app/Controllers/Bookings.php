<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Bookings extends BaseController
{
    private const STATUSES = ['baru', 'dikonfirmasi', 'selesai', 'dibatalkan'];

    public function index(): string
    {
        $bookings = $this->db()->table('bookings')->orderBy('event_date', 'DESC')->get()->getResultArray();

        return $this->render('bookings/index', ['title' => 'Pesanan tenda', 'bookings' => $bookings]);
    }

    public function new(): string
    {
        return $this->render('bookings/form', ['title' => 'Tambah pesanan', 'booking' => null, 'statuses' => self::STATUSES]);
    }

    public function create(): RedirectResponse
    {
        $rules = [
            'customer_name' => 'required|min_length[2]|max_length[120]',
            'phone' => 'permit_empty|max_length[30]',
            'event_date' => 'required|valid_date[Y-m-d]',
            'venue' => 'required|min_length[2]|max_length[180]',
            'total_amount' => 'required|decimal|greater_than_equal_to[0]',
            'paid_amount' => 'required|decimal|greater_than_equal_to[0]',
            'status' => 'required|in_list[baru,dikonfirmasi,selesai,dibatalkan]',
            'notes' => 'permit_empty|max_length[2000]',
        ];
        if (! $this->validate($rules) || (float) $this->request->getPost('paid_amount') > (float) $this->request->getPost('total_amount')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors() + ['paid_amount' => 'Uang muka tidak boleh melebihi nilai pesanan.']);
        }

        $db = $this->db();
        $code = 'TJ-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $paid = (float) $this->request->getPost('paid_amount');
        $db->transStart();
        $db->table('bookings')->insert([
            'booking_code' => $code,
            'customer_name' => trim((string) $this->request->getPost('customer_name')),
            'phone' => trim((string) $this->request->getPost('phone')) ?: null,
            'event_date' => $this->request->getPost('event_date'),
            'venue' => trim((string) $this->request->getPost('venue')),
            'total_amount' => $this->request->getPost('total_amount'),
            'paid_amount' => $paid,
            'status' => $this->request->getPost('status'),
            'notes' => trim((string) $this->request->getPost('notes')) ?: null,
            'created_by' => session('user_id'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $bookingId = $db->insertID();
        if ($paid > 0) {
            $db->table('finance_entries')->insert([
                'entry_date' => date('Y-m-d'),
                'type' => 'masuk',
                'category' => 'Uang muka pesanan',
                'description' => 'Uang muka ' . $code . ' - ' . trim((string) $this->request->getPost('customer_name')),
                'amount' => $paid,
                'booking_id' => $bookingId,
                'created_by' => session('user_id'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $db->transComplete();

        return redirect()->to('/bookings')->with('message', 'Pesanan ' . $code . ' berhasil disimpan.');
    }

    public function edit(int $id): string|RedirectResponse
    {
        $booking = $this->db()->table('bookings')->where('id', $id)->get()->getRowArray();
        if ($booking === null) {
            return redirect()->to('/bookings')->with('error', 'Pesanan tidak ditemukan.');
        }

        return $this->render('bookings/form', ['title' => 'Ubah pesanan', 'booking' => $booking, 'statuses' => self::STATUSES]);
    }

    public function update(int $id): RedirectResponse
    {
        $rules = [
            'customer_name' => 'required|min_length[2]|max_length[120]',
            'phone' => 'permit_empty|max_length[30]',
            'event_date' => 'required|valid_date[Y-m-d]',
            'venue' => 'required|min_length[2]|max_length[180]',
            'total_amount' => 'required|decimal|greater_than_equal_to[0]',
            'status' => 'required|in_list[baru,dikonfirmasi,selesai,dibatalkan]',
            'notes' => 'permit_empty|max_length[2000]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $db = $this->db();
        $booking = $db->table('bookings')->where('id', $id)->get()->getRowArray();
        if ($booking === null) {
            return redirect()->to('/bookings')->with('error', 'Pesanan tidak ditemukan.');
        }
        if ((float) $this->request->getPost('total_amount') < (float) $booking['paid_amount']) {
            return redirect()->back()->withInput()->with('error', 'Nilai pesanan tidak boleh lebih kecil dari pembayaran yang sudah tercatat.');
        }
        $db->table('bookings')->where('id', $id)->update([
            'customer_name' => trim((string) $this->request->getPost('customer_name')),
            'phone' => trim((string) $this->request->getPost('phone')) ?: null,
            'event_date' => $this->request->getPost('event_date'),
            'venue' => trim((string) $this->request->getPost('venue')),
            'total_amount' => $this->request->getPost('total_amount'),
            'status' => $this->request->getPost('status'),
            'notes' => trim((string) $this->request->getPost('notes')) ?: null,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/bookings')->with('message', 'Pesanan berhasil diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $db = $this->db();
        $db->transStart();
        $db->table('finance_entries')->where('booking_id', $id)->update(['booking_id' => null]);
        $db->table('bookings')->where('id', $id)->delete();
        $db->transComplete();

        return redirect()->to('/bookings')->with('message', 'Pesanan berhasil dihapus.');
    }
}
