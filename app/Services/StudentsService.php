<?php

namespace App\Services;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Student;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StudentsService
{
    /**
     * @param $id
     * @return the student with given $id
     * @throws HttpNotFoundxception when user is not found
     */
    public function findOne($id)
    {
        return Student::findOrFail($id);
    }

    /**
     * @param $email
     * @return the student with given $email or null if it doesn't exists
     */
    public function findOneByEmail($email)
    {
        return Student::where('email', $email)->first();
    }

    /**
     * @param $id
     * @return [$image, $mime] -- an array containing the image of the student and its mime type
     * @throws HttpNotFoundxception when user is not found or does not have image
     */
    public function showImage($id)
    {
        $student = $this->findOne($id);
        if (!$student->image)
            throw new NotFoundHttpException('User with id '.$id.' does not have any image');

        $image = Storage::get('public/'.$student->image);
        $mime = Storage::mimeType('public/'.$student->image);
        return [$image, $mime];
    }

    /**
     * @return a list of all students (without images)
     */
    public function findAll()
    {
        return Student::all();
    }

    /**
     * @param $studentRequest -- a StoreStudentRequest with all the data of the student
     * @return $student -- the student added to the database
     * @throws a validation errror if validation fails
     * @throws ConflictHttpException -- if there is another user with the same email
     * Stores the image (if there is) in public storage folder
     */
    public function create(StoreStudentRequest $studentRequest)
    {
        if ($this->findOneByEmail($studentRequest->input('email')))
        {
            throw new ConflictHttpException('There is another user with the same email');
        }

        $student = new Student($studentRequest->except('image'));

        $file = $studentRequest -> file('image');
        if ($file)
        {
            $student -> image = $file -> store('images', 'public');
        }

        $student -> save();
        return $student;
    }

    /**
     * @param $id -- id of the student to edit
     * @param $studentRequest -- a UpdateStudentRequest with all the data of the student
     * @return $student -- the student added to the database
     * @throws a validation errror if validation fails
     * @throws ConflictHttpException -- if there is another user with the same new email
     */
    public function update($id, UpdateStudentRequest $studentRequest)
    {
        $studentWithSameEmail = $this -> findOneByEmail($studentRequest->input('email'));
        if ($studentWithSameEmail && $studentWithSameEmail->id != $id)
        {
            throw new ConflictHttpException('There is another user with the same new email');
        }

        $student = $this -> findOne($id);

        $student -> update($studentRequest->except('image'));

        $file = $studentRequest -> file('image');
        if ($file)
        {
            if ($student->image != null)
                Storage::delete('public/'.$student->image);

            $student -> update(['image' => $file -> store('images', 'public')]);
        }

        return $this -> findOne($id);
    }

    /**
     * deletes a student (also deletes his/her image)
     * @param $id
     */
    public function delete($id)
    {
        $student = $this -> findOne($id);

        if ($student -> image != null)
            Storage::delete('public/'.$student->image);

        return $student->delete();
    }
}