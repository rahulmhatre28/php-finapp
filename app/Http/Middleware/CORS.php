<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use App\Traits\ApiResponser;

class CORS
{
    use ApiResponser;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try
        {
            if(!$request->headers->has('User-id')) {
                throw new Exception('User-id header is missing.');
            }
            
            $userid = $request->header('User-id');
            // get role of user
            $login_user = User::select(['role_id','parent_id'])->find($userid);
            $user = User::select('id')->where('parent_id',$userid)->with('children.children.children')->get();
            $parents = User::with('parent.parent.parent')->where('id',$userid)->first()->toArray();
            if(!empty($user)){
                $ids=[];
                $this->traversal($user,$ids);
                array_unshift($ids,$userid);
                $request->merge(['childs'=>$ids]);   
            }
            if(!empty($parents)){
                $ids1=[];
                $this->traversal_1($parents,$ids1);
                array_shift($ids1);
                $request->merge(['parents'=>$ids1]);   
            }
            $request->merge(['userid'=>$userid]);
            $request->merge(['roleid'=>$login_user->role_id]);
            $request->merge(['parentid'=>$login_user->parent_id]);
            file_put_contents('log',print_r($request->all(),true));
            return $next($request);
        }
        catch(Exception $e){
            return $this->error($e->getMessage(),401);
        }
    }

    protected function traversal($user,&$ids) {
        foreach($user as $a) {
            $ids[]=$a->id;
            if(isset($a->children)){
                $this->traversal($a->children,$ids);
            }
        }
    }
    protected function traversal_1($user,&$ids1) {
        $ids1[] = $user['id']; 
        if(isset($user['parent'])){
            $this->traversal_1($user['parent'],$ids1);
        }
    }
}
