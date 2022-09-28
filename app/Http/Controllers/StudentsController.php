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
     * @status HTTP_OK
     */
    public function index() 
    {
        return response()->json($this->studentsService->findAll(), Response::HTTP_OK);
    }

    /**
     * @param id
     * @return student with that id
     * @throws Not Found 404 if user does not exist
     * @status HTTP_OK
     */
    public function show($id)
    {
        return response()->json(new StudentResource($this->studentsService->findOne($id)), Response::HTTP_OK);
    }

    /**
     * @param id
     * @return image of the student with that id
     * @throws Not Found 404 if user does not exist or does not have image
     * @status HTTP_OK
     */
    public function showImage($id)
    {
        $image = $this->studentsService->showImage($id);
        return response($image[0], Response::HTTP_OK)
            ->header('Content-Type', $image[1]);
    }

    /**
     * @param $studentRequest - a StoreStudentRequest containing all data of a student
     * @return student created
     * @status HTTP_CREATED
     * @throws HTTP_CONFLICT if there is another user with the same email
     * @throws validation errors 422, if validation fails
     */
    public function create(StoreStudentRequest $studentRequest) 
    {
        return response()->json($this->studentsService->create($studentRequest), Response::HTTP_CREATED);
    }

    /**
     * @param $studentRequest - a UpdateStudentRequest containing all data of a student
     * @return student updated
     * @status HTTP_OK
     * @throws HTTP_CONFLICT if there is another user with the same email
     * @throws validation errors 422, if validation fails
     */
    public function update(UpdateStudentRequest $request, $id)
    {
        return response()->json($this->studentsService->update($request, $id), Response::HTTP_OK);
    }

    /**
     * deletes a studeing given $id
     * @param id
     * @return nothing
     * @status HTTP_NO_CONTENT
     * @throws HTTP_NOT_FOUND if user does not exist
     */
    public function delete($id)
    {
        $this->studentsService->delete($id);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
