<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\ExamGroup;
use App\Models\Grade;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * confirmation
     *
     * @param  mixed $id
     * @return void
     */
    public function confirmation($id)
    {
        //get exam group
        $exam_group = ExamGroup::with('exam.lesson', 'exam_session', 'student.classroom')
                    ->where('student_id', auth()->guard('student')->user()->id)
                    ->where('id', $id)
                    ->first();

        //get grade / nilai
        $grade = Grade::where('exam_id', $exam_group->exam->id)
                    ->where('exam_session_id', $exam_group->exam_session->id)
                    ->where('student_id', auth()->guard('student')->user()->id)
                    ->first();

        //return with inertia
        return inertia('Student/Exams/Confirmation', [
            'exam_group' => $exam_group,
            'grade' => $grade,
        ]);
    }

     /**
     * startExam
     *
     * @param  mixed $id
     * @return void
     */
    public function startExam($id)
    {
        //get exam group
        $exam_group = ExamGroup::with('exam.lesson', 'exam_session', 'student.classroom')
                    ->where('student_id', auth()->guard('student')->user()->id)
                    ->where('id', $id)
                    ->first();

        //get grade / nilai
        $grade = Grade::where('exam_id', $exam_group->exam->id)
                    ->where('exam_session_id', $exam_group->exam_session->id)
                    ->where('student_id', auth()->guard('student')->user()->id)
                    ->first();

        //update start time di table grades
        $grade->start_time = Carbon::now();
        $grade->update();

        //cek apakah questions / soal ujian di random
        if($exam_group->exam->random_question == 'Y') {

            //get questions / soal ujian
            $questions = Question::where('exam_id', $exam_group->exam->id)->inRandomOrder()->get();

        } else {

            //get questions / soal ujian
            $questions = Question::where('exam_id', $exam_group->exam->id)->get();

        }

        //define pilihan jawaban default
        $question_order = 1;

        foreach ($questions as $question) {

            //buat array jawaban / answer
            $options = [1,2];
            if(!empty($question->option_3)) $options[] = 3;
            if(!empty($question->option_4)) $options[] = 4;
            if(!empty($question->option_5)) $options[] = 5;

            //acak jawaban / answer
            if($exam_group->exam->random_answer == 'Y') {
                shuffle($options);
            }

            //cek apakah sudah ada data jawaban
            $answer = Answer::where('student_id', auth()->guard('student')->user()->id)
                    ->where('exam_id', $exam_group->exam->id)
                    ->where('exam_session_id', $exam_group->exam_session->id)
                    ->where('question_id', $question->id)
                    ->first();

            //jika sudah ada jawaban / answer
            if($answer) {

                //update urutan question / soal
                $answer->question_order = $question_order;
                $answer->update();

            } else {

                //buat jawaban default baru
                Answer::create([
                    'exam_id'           => $exam_group->exam->id,
                    'exam_session_id'   => $exam_group->exam_session->id,
                    'question_id'       => $question->id,
                    'student_id'        => auth()->guard('student')->user()->id,
                    'question_order'    => $question_order,
                    'answer_order'      => implode(",", $options),
                    'answer'            => 0,
                    'is_correct'        => 'N'
                ]);

            }
            $question_order++;

        }

        //redirect ke ujian halaman 1
        return redirect()->route('student.exams.show', [
            'id'    => $exam_group->id,
            'page'  => 1
        ]);
    }

    /**
     * show
     *
     * @param  mixed $id
     * @param  mixed $page
     * @return void
     */
    public function show($id, $page)
    {
        //get exam group
        $exam_group = ExamGroup::with('exam.lesson', 'exam_session', 'student.classroom')
                    ->where('student_id', auth()->guard('student')->user()->id)
                    ->where('id', $id)
                    ->first();

        if(!$exam_group) {
            return redirect()->route('student.dashboard');
        }

        //get all questions
        $all_questions = Answer::with('question')
                        ->where('student_id', auth()->guard('student')->user()->id)
                        ->where('exam_id', $exam_group->exam->id)
                        ->orderBy('question_order', 'ASC')
                        ->get();

        //count all question answered
        $question_answered = Answer::with('question')
                        ->where('student_id', auth()->guard('student')->user()->id)
                        ->where('exam_id', $exam_group->exam->id)
                        ->where('answer', '!=', 0)
                        ->count();


        //get question active
        $question_active = Answer::with('question.exam')
                        ->where('student_id', auth()->guard('student')->user()->id)
                        ->where('exam_id', $exam_group->exam->id)
                        ->where('question_order', $page)
                        ->first();

        //explode atau pecah jawaban
        if ($question_active) {
            $answer_order = explode(",", $question_active->answer_order);
        } else  {
            $answer_order = [];
        }

        //get duration
        $duration = Grade::where('exam_id', $exam_group->exam->id)
                    ->where('exam_session_id', $exam_group->exam_session->id)
                    ->where('student_id', auth()->guard('student')->user()->id)
                    ->first();

        //return with inertia
        return inertia('Student/Exams/Show', [
            'id'                => (int) $id,
            'page'              => (int) $page,
            'exam_group'        => $exam_group,
            'all_questions'     => $all_questions,
            'question_answered' => $question_answered,
            'question_active'   => $question_active,
            'answer_order'      => $answer_order,
            'duration'          => $duration,
        ]);
    }
}