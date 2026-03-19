@extends('layouts.app')

@section('title', 'Agenda')

@section('content')
<div class="container my-4 agenda-maintenance">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="kajian-divider"></div>

            <div class="kajian-section">
                <div class="kajian-header">
                    <div class="kajian-title">KAJIAN RUTIN</div>
                    <div class="kajian-note-line"></div>
                    <div class="kajian-subtitle">Kajian Kitab NASHOIHUDDINIYYAH</div>
                </div>

                <div class="kajian-card">
                    <div class="kajian-row">
                        <div class="kajian-label">Waktu</div>
                        <div class="kajian-value">Rutin Setiap Selasa Malam Rabu</div>
                    </div>
                    <div class="kajian-row">
                        <div class="kajian-label">Pukul</div>
                        <div class="kajian-value">19.30 WIB (Ba'da Isya s/d selesai)</div>
                    </div>
                    <div class="kajian-row">
                        <div class="kajian-label">Bersama</div>
                        <div class="kajian-value">
                            <ul class="kajian-speakers">
                                <li>al habib Abdullah bin Abubakar Alaydrus</li>
                                <li>KH. Syukron Muzaki</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="kajian-separator"></div>

                <div class="kajian-header">
                    <div class="kajian-subtitle">DZIKIR MANAKIB</div>
                </div>

                <div class="kajian-card">
                    <div class="kajian-row">
                        <div class="kajian-label">Waktu</div>
                        <div class="kajian-value">Rutin Setiap Hari Minggu Malam Senin</div>
                    </div>
                    <div class="kajian-row">
                        <div class="kajian-label">Pukul</div>
                        <div class="kajian-value">19.30 WIB (Ba'da Isya s/d selesai)</div>
                    </div>
                    <div class="kajian-row">
                        <div class="kajian-label">Bersama</div>
                        <div class="kajian-value">al ustadz Mahfudin</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
