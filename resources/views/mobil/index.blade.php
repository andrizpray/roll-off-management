@extends('layouts.app')

@section('title', 'Manajemen Mobil')
@section('page-title')
<i class="fas fa-truck mr-2 text-blue-500"></i>Manajemen Mobil
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($mobilStats as $stat)
        <a href="{{ route('mobil.show', $stat['mobil_id']) }}" class="card p-5 block hover:border-blue-300 transition" style="text-decoration:none;">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-truck text-blue-600"></i>
                    </div>
                    <div>
                        <div class="font-bold text-gray-900">{{ $stat['mobil_id'] }}</div>
                        <div class="text-xs text-gray-400">{{ $stat['total_assignments'] }} assignment(s)</div>
                    </div>
                </div>
                @if($stat['active_count'] > 0)
                    <span class="tag tag-yellow">
                        <i class="fas fa-circle-notch fa-spin" style="font-size:0.5rem"></i>
                        {{ $stat['active_count'] }} aktif
                    </span>
                @endif
            </div>
            @if($stat['last_assignment'])
                <div class="text-xs text-gray-400">
                    Terakhir: {{ $stat['last_assignment']->assigned_date->format('d M Y') }}
                    — {{ $stat['last_assignment']->driver_name }}
                </div>
            @endif
        </a>
    @empty
        <div class="md:col-span-3 card p-8 text-center text-gray-400">
            <i class="fas fa-truck text-3xl mb-3"></i>
            <p>Belum ada data kendaraan</p>
        </div>
    @endforelse
</div>
@endsection