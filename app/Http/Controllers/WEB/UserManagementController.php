<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use App\Services\UserService; // Import UserService
use Laravel\Telescope\Telescope;

class UserManagementController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }



    public function index(Request $request)
    {

        try {
            // Fetch users using the service
            $users = $this->userService->getAllUsers()->toArray();
           

            // Paginate the users manually
            $page = $request->input('page', 1);
            $perPage = 10;
            $offset = ($page - 1) * $perPage;
            $paginatedUsers = array_slice($users, $offset, $perPage, true);

            $usersPaginated = new LengthAwarePaginator(
                $paginatedUsers,
                count($users),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return view('user_management.user_list', ['users' => $usersPaginated]);
        } catch (\Exception $e) {
            Log::error('Error fetching users', ['error' => $e->getMessage(), 'trace' => $e->getTrace()]);
            return back()->with('error', 'Failed to fetch users.');
        }
    }


    public function edit($id)
    {
        try {
            // Fetch the user by ID using the service
            $user = $this->userService->getUserById($id);
            // Check if the user exists
            if (!$user) {
                return redirect()->route('users.index')->with('error', 'User not found.');
            }
            // Pass the user data to the edit view
            return view('user_management.edit_user', ['user' => $user]);
        } catch (\Exception $e) {
            Log::error('Error fetching user for editing', ['error' => $e->getMessage(), 'trace' => $e->getTrace()]);
            return back()->with('error', 'Failed to fetch user for editing.');
        }
    }

    public function show($id)
    {
        try {
            // Fetch the user by ID using the service
            $user = $this->userService->getUserById($id);
            // Check if the user exists
            if (!$user) {
                return redirect()->route('users.index')->with('error', 'User not found.');
            }
            // Pass the user data to the edit view
            return view('user_management.user_details', ['user' => $user]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch user ');
        }
    }
}