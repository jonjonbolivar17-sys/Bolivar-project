@extends('layouts.dashboard')

@section('title', 'Provision New Account · MDRRMO Arakan')

@section('content')
<x-command-shell active-role="admin" active-route="users" title="Provision New Account · MDRRMO Arakan">
    @php
        $arakanBarangays = [
            'Poblacion', 'Greenfield', 'Meocan', 'Doroluman', 'Gambodes', 'Badiangon',
            'Datu Ladayon', 'Kabalantian', 'Santo Niño', 'San Miguel', 'Maria Caridad',
            'Kulaman Valley', 'Malibatuan', 'Katipunan', 'Napalico', 'Salasang', 'Dallag',
            'Anapolon', 'Binoongan', 'Ganatan', 'Kinawayan', 'Libertad', 'Makalangot',
            'Naje', 'New Rosary', 'Sumalili', 'Tumanding', 'Lanao Kuran'
        ];
    @endphp

    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center justify-between border-b border-[#163b33] pb-4">
            <div>
                <a href="{{ route('admin.users.index') }}" class="text-xs font-mono text-emerald-400 hover:underline">← Return to User Directory</a>
                <h1 class="mt-2 text-2xl font-black tracking-tight text-[#F5FFFC]">Provision New Account</h1>
                <p class="text-xs text-[#7DB5A5]">Issue municipal credentials &amp; assign jurisdiction across Arakan, Cotabato</p>
            </div>
            <span class="rounded bg-emerald-500/20 border border-emerald-500/40 px-2 py-0.5 text-[9px] font-mono text-emerald-300 font-bold">
                NODE: DRRMO-ARK-01
            </span>
        </div>

        <div class="rounded-2xl border border-[#163b33] bg-[#0d1f1b] p-6 shadow-xl">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-[10px] font-mono uppercase text-[#7DB5A5] mb-1">
                        Full Officer / Citizen Name
                    </label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        placeholder="e.g. Officer Juan Perez"
                        class="w-full rounded-xl border border-[#163b33] bg-[#102621] py-2.5 px-3 text-xs md:text-sm text-[#F5FFFC] placeholder-[#7DB5A5] focus:border-emerald-500 focus:outline-none"
                    >
                    @error('name')
                        <p class="text-xs font-mono text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-[10px] font-mono uppercase text-[#7DB5A5] mb-1">
                        Official LGU / Email Address
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        placeholder="e.g. j.perez@arakan.gov.ph"
                        class="w-full rounded-xl border border-[#163b33] bg-[#102621] py-2.5 px-3 text-xs md:text-sm text-[#F5FFFC] placeholder-[#7DB5A5] focus:border-emerald-500 focus:outline-none"
                    >
                    @error('email')
                        <p class="text-xs font-mono text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role & Barangay Jurisdiction -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="role" class="block text-[10px] font-mono uppercase text-[#7DB5A5] mb-1">
                            System Role &amp; Access Tier
                        </label>
                        <select
                            id="role"
                            name="role"
                            required
                            class="w-full rounded-xl border border-[#163b33] bg-[#102621] py-2.5 px-3 text-xs md:text-sm text-[#F5FFFC] focus:border-emerald-500 focus:outline-none"
                        >
                            <option value="">Select Role</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Command Center Admin</option>
                            <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Field Staff / Responder</option>
                            <option value="reporter" {{ old('role') === 'reporter' ? 'selected' : '' }}>Citizen Reporter</option>
                        </select>
                        @error('role')
                            <p class="text-xs font-mono text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-mono uppercase text-[#7DB5A5] mb-1">
                            Arakan Barangay Jurisdiction
                        </label>
                        <select
                            name="barangay"
                            class="w-full rounded-xl border border-[#163b33] bg-[#102621] py-2.5 px-3 text-xs md:text-sm text-[#F5FFFC] focus:border-emerald-500 focus:outline-none"
                        >
                            @foreach ($arakanBarangays as $bgy)
                                <option value="{{ $bgy }}">Brgy. {{ $bgy }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Password -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-[10px] font-mono uppercase text-[#7DB5A5] mb-1">
                            Initial Access PIN / Password
                        </label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            class="w-full rounded-xl border border-[#163b33] bg-[#102621] py-2.5 px-3 text-xs md:text-sm text-[#F5FFFC] focus:border-emerald-500 focus:outline-none font-mono"
                        >
                        @error('password')
                            <p class="text-xs font-mono text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-[10px] font-mono uppercase text-[#7DB5A5] mb-1">
                            Confirm Access PIN / Password
                        </label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            required
                            class="w-full rounded-xl border border-[#163b33] bg-[#102621] py-2.5 px-3 text-xs md:text-sm text-[#F5FFFC] focus:border-emerald-500 focus:outline-none font-mono"
                        >
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#163b33]">
                    <a
                        href="{{ route('admin.users.index') }}"
                        class="rounded-xl border border-[#163b33] bg-[#102621] px-4 py-2.5 text-xs text-[#7DB5A5] hover:text-[#F5FFFC] transition"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-bold text-[#051613] hover:bg-emerald-500 transition shadow-lg"
                    >
                        + Provision Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-command-shell>
@endsection