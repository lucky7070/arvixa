<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Rules\CheckUnique;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\RolePermission;
use App\Models\UserPermission;
use App\Models\PermissionModule;
use \Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = User::select('users.id', 'users.name', 'users.email', 'users.slug', 'users.mobile', 'users.image', 'users.status', 'users.created_at', 'roles.name as role_name')
                ->where('users.id', '!=', 1)
                ->leftJoin('roles', 'roles.id', '=', 'users.role_id');
            return Datatables::of($data)->addIndexColumn()
                ->editColumn('image', function ($row) {
                    $btn = '<div class="avatar avatar-md"><img class="rounded-circle" src="' . asset('storage/' . $row['image']) . '" alt=""></div>';
                    return $btn;
                })
                ->editColumn('name', function ($row) {
                    return '<b class="text-dark">' . $row['name'] . '</b><br /> <span class="text-secondary">(' . $row['role_name'] . ')<span>';
                })
                ->editColumn('email', function ($row) {
                    return '<b class="text-dark">' . $row['email'] . '</b><br /> <b class="text-dark">' . $row['mobile'] . '<span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at']->format('d M, Y');
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (userCan(103, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('users.edit', $row['slug']) . '">Edit</a>';
                        $btn .= '<a class="dropdown-item" href="' . route('users.permission.view', $row['slug']) . '">Permission</a>';
                    }
                    if (userCan(103, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    if (userAllowed(103)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'email', 'name', 'image', 'status'])
                ->make(true);
        }
        return view('users.index');
    }

    public function add()
    {
        $roles = Role::where('status', 1)->where('id', '!=', 1)->get();
        return view('users.add', compact('roles'));
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'status'    => ['required', 'integer'],
            'role_id'   => ['required'],
            'email'     => ['required', new CheckUnique('users')],
            'mobile'    => ['required', 'digits:10', new CheckUnique('users'), 'regex:' . config('constant.phoneRegExp')],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
            'image'     => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
        ]);

        $data = [
            'slug'      => Str::uuid(),
            'role_id'   => $request->role_id,
            'name'      => $request->name,
            'email'     => $request->email,
            'mobile'    => $request->mobile,
            'status'    => $request->status,
            'image'     => 'admin/avatar.png',
            'password'  => Hash::make($request['password']),
        ];



        $path = 'admin';
        if ($file = $request->file('image')) {
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $data['image']        = $path . '/' . $uploadImage;
        }

        $user = User::create($data);

        $all_permissions = RolePermission::where('role_id', $request->role_id)->get();
        $data = [];
        foreach ($all_permissions as $key => $value) {
            array_push($data, [
                'user_id'       => $user->id,
                'module_id'     => $value->module_id,
                'can_view'      => $value->can_view,
                'can_add'       => $value->can_add,
                'can_edit'      => $value->can_edit,
                'can_delete'    => $value->can_delete,
                'allow_all'     => $value->allow_all,
            ]);
        }

        UserPermission::insert($data);
        return redirect(route('users'))->with('success', 'User Added Successfully!!');
    }

    public function edit($id)
    {
        $roles = Role::where('status', 1)->where('id', '!=', 1)->get();
        $user = User::firstWhere('slug', $id);
        if ($user == null) {
            return redirect(route('users'))->with('error', 'User Not Found!!');
        }
        return view('users.edit', compact(['user', 'roles']));
    }

    public function update(Request $request, $id)
    {
        $user = User::firstWhere('id', $id);
        if ($user == null) {
            return redirect(route('users'))->with('error', 'User Not Found!!');
        }

        $validated = [
            'role_id'   => ['required'],
            'status'    => ['required', 'integer'],
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', new CheckUnique('users', $user->id)],
            'mobile'    => ['required', 'digits:10', new CheckUnique('users', $user->id), 'regex:' . config('constant.phoneRegExp')],
            'image'     => ['image', 'mimes:jpg,png,jpeg', 'max:2048'],
        ];

        if ($request['password']) {
            $validated['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $request->validate($validated);
        $data = [
            'role_id'   => $request->role_id,
            'name'      => $request->name,
            'email'     => $request->email,
            'mobile'    => $request->mobile,
            'status'    => $request->status,
        ];

        if ($request['password']) {
            $data['password'] = Hash::make($request['password']);
        }

        $path = 'admin';
        if ($file = $request->file('image')) {
            $destinationPath    = 'public\\' . $path;
            $uploadImage        = time() . '_' . rand(99999, 1000000) . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($destinationPath . '/' . $uploadImage, file_get_contents($file));
            $data['image']        = $path . '/' . $uploadImage;
        }

        $user->update($data);
        return redirect(route('users'))->with('success', 'User Updated Successfully!!');
    }

    public function delete(Request $request)
    {
        if ($request->id) {
            $user = User::where('id', $request->id)->first();
            if ($user == null) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'User Not Found.',
                ]);
            }

            $user->delete();
            return response()->json([
                'success'   => true,
                'message'   => 'User deleted Successfully',
            ]);
        }
    }

    public function permission($id = null)
    {
        $user = User::firstWhere('slug', $id);
        if ($user == null) {
            return redirect(route('users'))->with('error', 'User Not Found!!');
        }

        $permissions = PermissionModule::select('user_permissions.*', 'permission_modules.id as modules_id', 'permission_modules.module_id as module_id', 'permission_modules.name')
            ->leftJoin('user_permissions', function ($join) use ($user) {
                $join->on('user_permissions.module_id', '=', 'permission_modules.module_id')
                    ->where('user_permissions.user_id', $user->id);
            })->get();

        if ($user == null) {
            return redirect(route('users'))->with('error', 'User Not Found!!');
        }

        return view('users.permission', compact('user', 'permissions'));
    }

    public function permission_update(Request $request)
    {
        $user_permission = UserPermission::firstWhere(['user_id' => $request->user_id, 'module_id' => $request->module_id]);
        if ($user_permission == null) {
            UserPermission::create([
                'user_id'       => $request->user_id,
                'module_id'     => $request->module_id,
                'can_view'      => $request->type == 'can_view' ? 1 : 0,
                'can_add'       => $request->type == 'can_add' ? 1 : 0,
                'can_edit'      => $request->type == 'can_edit' ? 1 : 0,
                'can_delete'    => $request->type == 'can_delete' ? 1 : 0,
                'allow_all'     => $request->type == 'allow_all' ? 1 : 0,
            ]);
            return true;
        }

        if (array($request->type, ['can_view', 'can_add', 'can_edit', 'can_delete', 'allow_all'])) {
            $user_permission->toggle($request->type);
            return  true;
        }
        return false;
    }
}
