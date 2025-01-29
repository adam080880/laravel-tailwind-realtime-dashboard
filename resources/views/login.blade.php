@extends('template.main')

@section('content')
  <div class="w-full h-svh bg-primary flex justify-center items-center">
    <div class="bg-background p-8 rounded-lg shadow-lg w-full max-w-[400px] mx-2">
      <h1 class="text-2xl font-bold text-center">Login</h1>
      <form action="{{ route('login.api') }}" method="post" class="mt-4">
        @csrf
        <div class="mb-4">
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input type="email" name="email" id="email" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" required>
          @if ($errors->has('email'))
            <span class="text-red-500 text-xs mt-1">{{ $errors->first('email') }}</span>
          @endif
        </div>
        <div class="mb-4">
          <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
          <input type="password" name="password" id="password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" required>
          @if ($errors->has('password'))
            <span class="text-red-500 text-xs mt-1">{{ $errors->first('password') }}</span>
          @endif
        </div>
        <div class="flex flex-col gap-4">
          <button type="submit" class="bg-primary text-background px-4 py-2 rounded-md">Login</button>
          <a href="{{ route('register') }}" class="text-center text-primary hover:underline">Register</a>
        </div>
      </form>

    </div>
  </div>
@endsection
