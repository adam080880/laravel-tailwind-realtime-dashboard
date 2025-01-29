@extends('template.main')

@section('content')
  <div class="w-full min-h-svh bg-background">
    <nav class="bg-white w-full border-b border-gray-200">
      <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
        <a href="" class="flex items-center space-x-3 rtl:space-x-reverse">
          <span class="self-center text-2xl font-semibold whitespace-nowrap">Dashboard</span>
        </a>
        <div class="flex space-x-3 md:space-x-0 rtl:space-x-reverse">
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

