@extends('template.main')

@section('content')
  <div class="w-full min-h-svh bg-white">
    <nav class="w-full border-b border-gray-200" style="background: linear-gradient(180deg, rgba(255,255,83,1) 0%, rgba(255,148,83,1) 100%);">
      <div class="flex flex-wrap items-stretch justify-between mx-auto">
        <div class="flex flex-row items-center gap-[12px]">
          <div class="bg-white flex items-center pr-[50px] h-full" style="border-radius: 0 0 300px 0;">
            <a href="" class="pl-4">
              <img src="/assets/appIcon.png" alt="img-icon" class="aspect-[170/47] w-[170px] h-auto" />
            </a>
          </div>
          <a href="/" class="block px-3 text-[20px] font-black text-[#1A47AA] rounded-sm" aria-current="page">Home</a>
          <a href="/stream" class="block px-3 text-[20px] font-black text-[#1A47AA] rounded-sm" aria-current="page">Stream</a>
        </div>
        <div class="flex items-center space-x-3 md:space-x-0 rtl:space-x-reverse p-4">
          <a href="{{ route('logout') }}">
            <button type="button" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Logout</button>
          </a>
        </div>
      </div>
    </nav>
    <div class="max-w-screen-xl flex flex-row mx-auto p-4">
      @yield('dashboard_content')
    </div>
  </div>
@endsection('content')

