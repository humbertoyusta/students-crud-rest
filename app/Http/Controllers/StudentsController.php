<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;
use App\Services\StudentsService;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;

class StudentsController extends Controller
{
    private StudentsService $studentsService;

    /**
     * Constructor
     * Injecting studentsService
     */
    public function __construct(StudentsService $studentsService)
    {
        $this->studentsService = $studentsService;
    }

    /**
     * @return a list of students
     */
    public function index() 
    {
        return response()->json($this->studentsService->findAll(), Response::HTTP_OK);
    }

    /**
     * @return a view with student's data
     * @param id
     */
    public function show($id)
    {
        return response()->json(new StudentResource($this->studentsService->findOne($id)), Response::HTTP_OK);
    }

    public function showImage($id)
    {
        return response($this->studentsService->showImage($id), Response::HTTP_OK);
    }

    /**
     * Adds a new student
     * @param $request - a request containing all data of a student
     */
    public function create(StoreStudentRequest $request) 
    {
        return response()->json($this->studentsService->create($request), Response::HTTP_CREATED);
    }

    /**
     * Edits a student
     * @redirects to the same edit view with a success message or an error
     * @param $request - a request containing the new data of the student
     */
    public function update(UpdateStudentRequest $request, $id)
    {
        return response()->json($this->studentsService->update($request, $id), Response::HTTP_OK);
    }

    /**
     * deletes a studeing given $id
     * @param id
     * @return a view of the list of students and a success message or error
     */
    public function delete($id)
    {
        $this->studentsService->delete($id);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
