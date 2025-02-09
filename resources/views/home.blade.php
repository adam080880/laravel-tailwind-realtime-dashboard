@extends('template.dashboard')

@section('dashboard_content')
  <div class="w-full" id="main-content">
    <div class="container mx-auto">
      <div class="flex flex-row flex-wrap mb-6">
        <div class="flex-1">
          <h1 class="mb-2 text-[40px] font-extrabold">Hello,<br />{{$user->name}}</h1>
          <div class="p-[16px] bg-[#D9D9D9] rounded-[12px]">
            <form action="/change-password" method="POST">
              {{ csrf_field() }}
              <p class="mb-[12px] font-bold">Change Password</p>

              <div class="grid gap-4 bg-[#B5B2B2] p-[12px] py-[16px] rounded-[12px]">
                <div>
                    <label for="old_password" class="block mb-2 text-sm font-medium text-gray-900">Old password</label>
                    <input type="password" id="old_password" name="old_password" class="bg-[#D9D9D9] border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="" required />
                    @if ($errors->has('old_password'))
                      <span class="text-red-500 text-xs mt-1">{{ $errors->first('old_password') }}</span>
                    @endif
                </div>
                <div>
                    <label for="new_password" class="block mb-2 text-sm font-medium text-gray-900">New password</label>
                    <input type="password" id="new_password" name="new_password" class="bg-[#D9D9D9] border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="" required />
                    @if ($errors->has('new_password'))
                      <span class="text-red-500 text-xs mt-1">{{ $errors->first('new_password') }}</span>
                    @endif
                </div>
                <div>
                    <label for="confirm_new_password" class="block mb-2 text-sm font-medium text-gray-900">Confirm password</label>
                    <input type="password" id="confirm_new_password" name="confirm_new_password" class="bg-[#D9D9D9] border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="" required />
                    @if ($errors->has('confirm_new_password'))
                      <span class="text-red-500 text-xs mt-1">{{ $errors->first('confirm_new_password') }}</span>
                    @endif
                </div>

                @if (session('success_message'))
                  <div class="p-4 mb-4 text-sm text-white rounded-lg bg-green-500" role="alert">
                    <span class="font-medium">Success</span> {{ session('success_message') }}
                  </div>
                @endif
              </div>

              <button type="submit" class="flex justify-self-end text-white mt-4 bg-primary hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Submit</button>
            </form>
          </div>
        </div>

        <img src="/assets/home.png" alt="img-home" class="min-w-[517px] max-w-[40%] aspect-[517/555] h-auto">
      </div>

      @if($user->role == 2)
        <div>
          <div class="flex items-center justify-between mb-2">
            <h2 class="text-[24px] font-bold">Users</h2>
            <button onclick="showModal()" type="button" class="text-white bg-primary hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">Buat Baru</button>
          </div>
          <div class="bg-[#D9D9D9] border border-gray-300 rounded-[12px] overflow-hidden">
            <table class="w-full border border-gray-300 border-collapse rounded-[12px]">
              <thead>
                <tr class="text-left">
                  <th class="py-2 pl-4">No</th>
                  <th class="py-2 pl-4">Nama</th>
                  <th class="py-2 pl-4">Role</th>
                  <th class="py-2 pl-4">Status</th>
                  <th class="py-2 pl-4">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $no = 1;
                @endphp
                @foreach ($users as $user)
                  <tr class="border-t border-gray-300 bg-white">
                    <td class="p-2 pl-4">{{ $no++ }}</td>
                    <td class="p-2 pl-4 w-[50%]">{{ $user->name }}</td>
                    <td class="p-2 pl-4">{{ $user->role == 2 ? 'Admin' : 'User' }}</td>
                    <td class="p-2 pl-4">
                      <span class="text-[14px] px-2 py-1 rounded-md {{$user->verified ? 'bg-green-200 text-green-600' : 'bg-yellow-200 text-yellow-600'}}">
                        {{ $user->verified ? 'Disetujui' : 'Pending' }}
                      </span>
                    </td>
                    <td class="p-2 pl-4">
                      <div class="flex items-center gap-2">
                        <button onclick="showModal({{$user->id}})" type="button" class="text-white bg-primary hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">Detail</button>
                        @if ($user->verified == 0)
                          <button onclick="verifyConfirm({{$user->id}})" type="button" class="text-white bg-green-500 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">Setujui</button>
                        @endif
                        @if ($user->id != 1)
                          <button onclick="deleteConfirm({{$user->id}})" type="button" class="text-white bg-red-500 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">Hapus</button>
                        @endif
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif
    </div>
  </div>

  <div id="edit-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 bottom-0 z-[2000] justify-center items-center w-full md:inset-0 max-h-full p-4 bg-black bg-opacity-50">
    <div class="relative p-4 w-full max-w-md max-h-full">
      <!-- Modal content -->
      <div class="relative bg-white rounded-lg shadow-sm">
          <!-- Modal header -->
          <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
              <h3 class="text-xl font-semibold text-gray-900">
                  Detail User
              </h3>
              <button onclick="closeModal()" type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                  <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                  </svg>
                  <span class="sr-only">Close modal</span>
              </button>
          </div>
          <!-- Modal body -->
          <div class="p-4 md:p-5">
              <form class="space-y-4" action="/update-user" method="POST">
                  {{ csrf_field() }}
                  <input type="hidden" id="edit-userId" name="userId" value="" />
                  <div>
                      <label for="edit-email" class="block mb-2 text-sm font-medium text-gray-900">Email <span style="color: red">*</span></label>
                      <input type="email" name="email" id="edit-email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="name@company.com" required />
                  </div>
                  <div>
                      <label for="edit-name" class="block mb-2 text-sm font-medium text-gray-900">Nama <span style="color: red">*</span></label>
                      <input type="name" name="name" id="edit-name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Nama" required />
                  </div>
                  <div>
                      <label for="edit-role" class="block mb-2 text-sm font-medium text-gray-900">Role <span style="color: red">*</span></label>
                      <select name="role" id="edit-role" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Nama" required>
                        <option value="1">User</option>
                        <option value="2">Admin</option>
                      </select>
                  </div>
                  <div>
                      <label for="edit-password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                      <input type="password" name="password" id="edit-password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" />
                  </div>
                  <button id="btn-submit" type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update</button>
              </form>
          </div>
      </div>
    </div>
</div> 

  <script>
    const users = @json($users);
    const userById = users.reduce((prev, user) => {
      return {
        ...prev,
        [`${user.id}`]: user
      }
    }, {});

    const showModal = userId => {
      const user = userById[`${userId}`] || {};

      document.getElementById('edit-email').value = user.email || '';
      document.getElementById('edit-name').value = user.name || '';
      document.getElementById('edit-role').value = user.role || '';
      document.getElementById('edit-userId').value = user.id || '';
      document.getElementById('btn-submit').innerHTML = user.id ? 'Update' : 'Buat';

      if (+userId === 1) {
        document.getElementById('edit-role').setAttribute('disabled', 'disabled');
      } else {
        document.getElementById('edit-role').removeAttribute('disabled');
      }

      if (!+userId) {
        document.getElementById('edit-password').setAttribute('required', 'required');
      } else {
        document.getElementById('edit-password').removeAttribute('required');
      }

      document.getElementById('edit-modal').classList.remove('hidden');
      document.getElementById('edit-modal').classList.add('flex');
    };

    const closeModal = () => {
      document.getElementById('edit-modal').classList.remove('flex');
      document.getElementById('edit-modal').classList.add('hidden');
    };

    const deleteConfirm = (userId) => {
      const user = userById[`${userId}`];

      Swal.fire({
        title: 'Konfirmasi',
        text: `Kamu akan menghapus ${user.name} dari daftar user`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Konfirmasi'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = `/delete-user?userId=${userId}`;
        }
      });
    };

    const verifyConfirm = (userId) => {
      const user = userById[`${userId}`];

      Swal.fire({
        title: 'Konfirmasi',
        text: `Kamu akan verifikasi ${user.name}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Konfirmasi'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = `/confirm-user?userId=${userId}`;
        }
      });
    };

    window.onload = async function() {
      @if (session('user_success_message'))
        await Swal.fire({
          title: 'Success',
          text: '{{ session('user_success_message') }}',
          icon: 'success',
          confirmButtonText: 'OK'
        });
      @endif
    }
  </script>
@endsection
