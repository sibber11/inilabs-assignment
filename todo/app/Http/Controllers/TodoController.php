<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteManyTodoRequest;
use App\Models\Todo;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use Inertia\Inertia;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // get all the todos from the database
        // paginate the todos
        // return the todos to the view
        $todos = Todo::orderBy('id', 'desc')->paginate(6);
        return Inertia::render('Todo', [
            'todos' => $todos
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTodoRequest $request)
    {
        // create a new todo with the validated data
        $todo = Todo::create($request->validated());

        // return back with a success message
        return back()->with('success', 'Todo successfully saved.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTodoRequest $request, Todo $todo)
    {
        
        // if the request has status field
        // update the status of the todo
        // else update the todo with the validated data
        
        if ($request->has('status')) {
            $todo->status = $request->status;
        } else {
            $todo->fill($request->validated());
        }

        // save the todo
        $todo->save();

        // return back with a success message
        return back()->with('success', 'Todo updated saved.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        // delete the todo
        $todo->delete();

        // return back with a success message
        return back()->with('success', 'Todo deleted successfully.');
    }

    /**
     * Remove the specified resources from storage.
     */
    public function destroyMany(DeleteManyTodoRequest $request)
    {
        // delete the todos
        Todo::destroy($request->validated('todos'));

        // return back with a success message
        return back()->with('success', 'Todos deleted successfully.');
    }
}
