<?php

namespace App\Http\Controllers;

use App\Http\Common\Util;
use App\Models\Role;
use App\Http\Services\RoleService;
use Exception;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * @var roleService
     */
    protected $roleService;

    /**
     * PostController Constructor
     *
     * @param roleService $roleService
     *
     */
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = $this->roleService->getAll();
            return Util::response(response(),['status'=>1,'result'=>$data,'errorcode'=>null,'defaultError'=>null]);
        } catch (Exception $e) {
            return Util::response(response(),['status'=>0,'result'=>null,'errorcode'=>500,'defaultError'=>$e->getMessage()]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = $this->roleService->saveRoleData($request);
            return Util::response(response(),['status'=>1,'result'=>$data,'errorcode'=>null,'defaultError'=>null]);
        } catch (Exception $e) {
            return Util::response(response(),['status'=>0,'result'=>null,'errorcode'=>500,'defaultError'=>$e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = ['status' => 200];

        try {
            $result['data'] = $this->roleService->getById($id);
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }
        return response()->json($result, $result['status']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Post $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $post)
    {
        //
    }

    /**
     * Update post.
     *
     * @param Request $request
     * @param id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $data = $this->roleService->updateRole($request, $request->id);
            return Util::response(response(),['status'=>1,'result'=>$data,'errorcode'=>null,'defaultError'=>null]);
        } catch (Exception $e) {
            return Util::response(response(),['status'=>0,'result'=>null,'errorcode'=>500,'defaultError'=>$e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = ['status' => 200];

        try {
            $result['data'] = $this->roleService->deleteById($id);
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }
        return response()->json($result, $result['status']);
    }

    public function getById(Request $request)
    {
        try {
            $id = $request->roleid;
            $data = $this->roleService->getById($id);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(),200);
        }
    }
}