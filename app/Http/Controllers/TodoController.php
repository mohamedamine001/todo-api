<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;
use Exception;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * @OA\Get(
     *      path="/user/todos",
     *      operationId="getAllTodos",
     *      tags={"Todo"},

     *      summary="Get List Of Todos",
     *      description="Returns all todos created by currently authenticated user",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *  )
     */


    public function index()
    {
        //
        $todos = Auth::user()->todos;
        return response()->json(["status"=>"success","error"=>false,"count"=>count($todos),"data"=>$todos],200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Post(
     *      path="/user/todos",
     *      operationId="addingNewTodo",
     *      tags={"addTodo"},

     *      summary="Adding TODO",
     *      description="Adding a new Todo",
     *      @OA\Response(
     *          response=201,
     *          description="Todo Added Successfully",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Validation Errors",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *  )
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(),[
            "title"       => "required|min:3|unique:todos,title",
            "description" => "required"
        ]);

        if($validator->fails()){
            return $this->validationErrors($validator->errors());
        }
        try{
            $todo = Todo::create([
                "title"=>$request->title,
                "description"=>$request->description,
                "user_id"=>Auth::user()->id
            ]);
            return response()->json(["status"=>"success","error"=>false,"message"=>"Todo Added Sucessfully!"],201);
        }catch(Exception $exception){
            return response()->json(["status"=>"failed","error"=>$exception->getMessage()],404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *      path="/user/todos/{id}",
     *      operationId="getTodoByID",
     *      tags={"Todo"},

     *      summary="Get Todo by ID",
     *      description="Returns Todo by its id",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *      ),
     *       @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *          type="integer"
     *       )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *  )
     */
    public function show($id)
    {
        //
        $todo = Auth::user()->todos->find($id);

        if($todo){
          return response()->json(["status"=>"success","error"=>false,"data"=>$todo],200);
        }else{
          return response()->json(["status"=>"failed","error"=>true,"message"=>"Not Todo Found !"],404);
        }
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Put(
     *      path="/user/todos/{id}",
     *      operationId="updateTodo",
     *      tags={"Todo"},

     *      summary="Update Todo",
     *      description="Todo Updating",
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *          type="integer"
     *       )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Validation Error",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *  )
     */
    public function update(Request $request, $id)
    {
        //
        $todo = Auth::user()->todos->find($id);
        if($todo){
            $validator = Validator::make($request->all(),[
                "title"=>"required",
                "description"=>"required"
            ]);

            if($validator->fails()){
                return $this->validationErrors($validator->errors());
            }

            $todo['title'] = $request->title;
            $todo['description'] = $request->description;

            if($request->active){
                $todo['active'] = $request->active;
            }

            if($request->finished){
                $todo['finished'] = $request->finished;
            }

            $todo->save();

            return response()->json(["status"=>"success","error"=>false,"message"=>"Todo Updated Successfully!"],201);
        }

        return response()->json(["status"=>"failes","error"=>true,"message"=>"Todo Not Found."],404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Delete(
     *      path="/user/todos/{id}",
     *      operationId="deleteTodo",
     *      tags={"Todo"},

     *      summary="Delete Todo",
     *      description="Delete Todo By ID",
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *          type="integer"
     *       )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *  )
     */
    public function destroy($id)
    {
        //
        $todo = Auth::user()->todos->find($id);
        if($todo){
            $todo->delete();
            return response()->json(["status"=>"sucess","error"=>false,"message"=>"Todo deleted Successfully"],201);
        }else{
            return response()->json(["status"=>"failed","error"=>true,"message"=>"Todo Not Found"],404);
        }
    }
}
