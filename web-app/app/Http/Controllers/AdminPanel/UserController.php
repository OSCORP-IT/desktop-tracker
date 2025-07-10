<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $users = User::query()
            ->orderBy('employee_id', 'asc')
            ->get();

        return view('admin_panel.users.index', compact('users'));
    }

    private function data(User $user) {
        $status = User::$status;

        return [
            'user' => $user,
            'status' => $status,
            'roles' => Role::get(['id', 'name'])
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin_panel.users.create', $this->data(new User()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validated_data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => ["required", "in:" . implode(',', User::$genders)],
            'date_of_birth' => ['nullable', 'string', 'max:255'],
            'mobile_number' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed','min:6'],
            'position' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:10240'],
            'input_status' => ["required", "in:" . implode(',', User::$status)],
            'role_ids' => ['required', 'array'],
        ]);

        DB::beginTransaction();

        try {
            $profile_image_name = null;

            if ($profile_image = $request->file('profile_image')) {
                $destination_path = 'images/users/';
                $profile_image_name = date('YmdHis') . '.' . $profile_image->getClientOriginalExtension();
                $profile_image->move($destination_path, $profile_image_name);
            }

            $user = User::create([
                'employee_id' => $this->get_new_employee_id(),
                'name' => $validated_data['name'],
                'gender' => $validated_data['gender'],
                'date_of_birth' => $validated_data['date_of_birth'],
                'mobile_number' => $validated_data['mobile_number'],
                'email' => $validated_data['email'],
                'password' => Hash::make($validated_data['password']),
                'position' => $validated_data['position'],
                'address' => $validated_data['address'],
                'profile_image' => ($request->file('profile_image')) ? $profile_image_name : null,
                'status' => $validated_data['input_status']
            ]);

            if ($request->role_ids) {
                $user->assignRole($request->role_ids);
            }

            DB::commit();

            return redirect()->to('admin-panel/users')
                ->with('success', 'Created Successfully.');
        }
        catch (\Exception $e) {
            DB::rollback();

            return back()
                ->with('error', 'An error occurred while creating the data. ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user) {
        if ($user->id != 1) {
            return view('admin_panel.users.show', $this->data($user));
        }
        else {
            return abort(404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user) {
        if ($user->id != 1) {
            $user_roles = $user->roles ?? [];
            $user_role_ids = Array();
    
            foreach($user_roles as $user_role) {
                $user_role_ids[] = $user_role->id;
            }

            return view('admin_panel.users.edit', $this->data($user) + [
                'role_ids' => $user_role_ids
            ]);
        }
        else {
            return abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user) {
        if ($user->id != 1) {
            $validated_data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'gender' => ["required", "in:" . implode(',', User::$genders)],
                'date_of_birth' => ['nullable', 'string', 'max:255'],
                'mobile_number' => ['required', 'string', 'max:255', 'unique:users,mobile_number,'.$user->id],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
                'password' => ['nullable', 'confirmed', 'min:6'],
                'position' => ['nullable', 'string', 'max:255'],
                'address' => ['nullable', 'string', 'max:255'],
                'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:10240'],
                'input_status' => ["required", "in:" . implode(',', User::$status)],
                'role_ids' => ['required', 'array'],
            ]);
    
            DB::beginTransaction();
    
            try {
                $profile_image_name = null;

                if ($profile_image = $request->file('profile_image')) {
                    if ($user->profile_image && file_exists('images/users/' . $user->profile_image)) {
                        unlink('images/users/' . $user->profile_image);
                    }

                    $destination_path = 'images/users/';
                    $profile_image_name = date('YmdHis') . '.' . $profile_image->getClientOriginalExtension();
                    $profile_image->move($destination_path, $profile_image_name);
                }
                else {
                    $profile_image_name = $user->profile_image;
                }
                
                $user->update([
                    'name' => $validated_data['name'],
                    'date_of_birth' => $validated_data['date_of_birth'],
                    'gender' => $validated_data['gender'],
                    'position' => $validated_data['position'],
                    'mobile_number' => $validated_data['mobile_number'],
                    'email' => $validated_data['email'],
                    'password' => Hash::make($validated_data['password']),
                    'security' => $validated_data['password'],
                    'address' => $validated_data['address'],
                    'profile_image' => $profile_image_name,
                    'status' => $validated_data['input_status']
                ]);
    
                if ($request->role_ids) {
                    DB::table('model_has_roles')->where('model_id', $user->id)->delete();
                    $user->assignRole($request->role_ids);
                }
    
                DB::commit();
    
                return redirect()->to('admin-panel/users')
                    ->with('success', 'Updated Successfully.');
            }
            catch (\Exception $e) {
                DB::rollback();
    
                return back()
                    ->with('error', 'An error occurred while updating the data. ' . $e->getMessage())
                    ->withInput();
            }
        }
        else {
            return abort(404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user) {
        if ($user->id != 1) {
            DB::beginTransaction();

            try {
                $user->delete();

                DB::commit();

                return redirect()->to('admin-panel/users')
                    ->with('success', 'Deleted Successfully.');
            } 
            catch (\Exception $e) {
                DB::rollback();

                return back()
                    ->with('error', 'An error occurred while deleting the record. ' . $e->getMessage());
            }
        }
        else {
            return abort(404);
        }
    }

    public function get_new_employee_id() {
        $latest_user = User::withTrashed()->orderBy('employee_id', 'desc')->first();

        if (!$latest_user || !$latest_user->employee_id) {
            return 'EMP_001';
        }

        $last_id = (int) substr($latest_user->employee_id, 4);
        $new_id = str_pad($last_id + 1, 3, '0', STR_PAD_LEFT);
        
        return 'EMP_' . $new_id;
    }
}
