<?php

namespace App\Http\Controllers;

class AboutController extends Controller
{
    public function index()
    {
        return '
            <h2>Profil Toko</h2>
            <p><strong>Nama Toko:</strong> Fadli Store</p>
            <p><strong>Deskripsi:</strong> Menjual berbagai kebutuhan elektronik dan aksesoris dengan harga terjangkau.</p>
            <p><strong>Berdiri sejak:</strong> 2024</p>
        ';
    }
}