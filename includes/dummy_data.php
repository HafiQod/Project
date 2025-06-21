<?php
// includes/dummy_data.php
// Data dummy untuk template homepage

function getDummyDonations() {
    return [
        [
            'id' => 1,
            'penyumbang_nama' => 'Ahmad Susanto',
            'penyumbang_foto' => 'images/profile/user1.jpg',
            'penyumbang_kontribusi' => 15,
            'nama_makanan' => 'Nasi Goreng',
            'foto_makanan' => 'images/food/nasi-goreng.jpg',
            'kategori' => 'Makanan Basah',
            'status' => 'Tersedia',
            'lokasi' => 'Yogyakarta',
            'jumlah_porsi' => 5,
            'deskripsi' => 'Nasi goreng spesial dengan telur dan ayam',
            'waktu_posting' => '2024-06-21 10:30:00',
            'expired_time' => '2024-06-21 18:00:00'
        ],
        [
            'id' => 2,
            'penyumbang_nama' => 'Siti Nurhaliza',
            'penyumbang_foto' => 'images/profile/user2.jpg',
            'penyumbang_kontribusi' => 28,
            'nama_makanan' => 'Croissant Fresh',
            'foto_makanan' => 'images/food/croissant.jpg',
            'kategori' => 'Makanan Kering',
            'status' => 'Sudah habis',
            'lokasi' => 'Bantul',
            'jumlah_porsi' => 8,
            'deskripsi' => 'Croissant segar dari toko roti',
            'waktu_posting' => '2024-06-21 08:15:00',
            'expired_time' => '2024-06-21 20:00:00'
        ],
        [
            'id' => 3,
            'penyumbang_nama' => 'Budi Santoso',
            'penyumbang_foto' => 'images/profile/user3.jpg',
            'penyumbang_kontribusi' => 7,
            'nama_makanan' => 'Mie Instan Korea',
            'foto_makanan' => 'images/food/mie-korea.jpg',
            'kategori' => 'Makanan Kering',
            'status' => 'Tersedia',
            'lokasi' => 'Sleman',
            'jumlah_porsi' => 12,
            'deskripsi' => 'Mie instan Korea berbagai rasa',
            'waktu_posting' => '2024-06-21 09:45:00',
            'expired_time' => '2024-06-22 12:00:00'
        ],
        [
            'id' => 4,
            'penyumbang_nama' => 'Maya Sari',
            'penyumbang_foto' => 'images/profile/user4.jpg',
            'penyumbang_kontribusi' => 42,
            'nama_makanan' => 'Soto Ayam',
            'foto_makanan' => 'images/food/soto-ayam.jpg',
            'kategori' => 'Makanan Basah',
            'status' => 'Tersedia',
            'lokasi' => 'Yogyakarta',
            'jumlah_porsi' => 10,
            'deskripsi' => 'Soto ayam lamongan dengan kuah kuning',
            'waktu_posting' => '2024-06-21 11:20:00',
            'expired_time' => '2024-06-21 19:00:00'
        ],
        [
            'id' => 5,
            'penyumbang_nama' => 'Rizki Pratama',
            'penyumbang_foto' => 'images/profile/user5.jpg',
            'penyumbang_kontribusi' => 23,
            'nama_makanan' => 'Buah-buahan Segar',
            'foto_makanan' => 'images/food/buah-segar.jpg',
            'kategori' => 'Serat',
            'status' => 'Tersedia',
            'lokasi' => 'Bantul',
            'jumlah_porsi' => 15,
            'deskripsi' => 'Campuran buah segar: apel, jeruk, pisang',
            'waktu_posting' => '2024-06-21 07:30:00',
            'expired_time' => '2024-06-21 16:00:00'
        ],
        [
            'id' => 6,
            'penyumbang_nama' => 'Dewi Lestari',
            'penyumbang_foto' => 'images/profile/user6.jpg',
            'penyumbang_kontribusi' => 35,
            'nama_makanan' => 'Ayam Bakar',
            'foto_makanan' => 'images/food/ayam-bakar.jpg',
            'kategori' => 'Protein',
            'status' => 'Tersedia',
            'lokasi' => 'Sleman',
            'jumlah_porsi' => 6,
            'deskripsi' => 'Ayam bakar bumbu kecap dengan nasi',
            'waktu_posting' => '2024-06-21 12:00:00',
            'expired_time' => '2024-06-21 21:00:00'
        ]
    ];
}

function getFilterCategories() {
    return [
        'Semua',
        'Makanan Basah',
        'Makanan Kering', 
        'Protein',
        'Serat',
        'Minuman',
        'Snack'
    ];
}

function getFilterStatuses() {
    return [
        'Semua',
        'Tersedia',
        'Sudah habis',
        'Expired'
    ];
}

function filterDonations($donations, $category = 'Semua', $status = 'Semua') {
    if ($category === 'Semua' && $status === 'Semua') {
        return $donations;
    }
    
    return array_filter($donations, function($donation) use ($category, $status) {
        $categoryMatch = ($category === 'Semua') || ($donation['kategori'] === $category);
        $statusMatch = ($status === 'Semua') || ($donation['status'] === $status);
        
        return $categoryMatch && $statusMatch;
    });
}

function getTimeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'Baru saja';
    if ($time < 3600) return floor($time/60) . ' menit yang lalu';
    if ($time < 86400) return floor($time/3600) . ' jam yang lalu';
    if ($time < 2629746) return floor($time/86400) . ' hari yang lalu';
    
    return date('d M Y', strtotime($datetime));
}
?>