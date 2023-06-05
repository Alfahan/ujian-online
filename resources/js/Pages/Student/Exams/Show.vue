<template>
    <Head>
        <title>Ujian Dengan Nomor Soal : {{ page }} - Aplikasi Ujian Online</title>
    </Head>
    <div class="row mb-5">
        <div class="col-md-7">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="mb-0">Soal No. <strong class="fw-bold">{{ page }}</strong></h5>
                        </div>
                        <div>
                            <span class="badge bg-info p-2"> <i class="fa fa-clock"></i> Timer Disini</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <div v-if="question_active !== null">

                        <div>
                            <p v-html="question_active.question.question"></p>
                        </div>

                        <table>
                            <tbody>
                                <tr v-for="(answer, index) in answer_order" :key="index">
                                    <td width="50" style="padding: 10px;">

                                        <button v-if="answer == question_active.answer" class="btn btn-info btn-sm w-100 shdaow">{{ options[index] }}</button>

                                        <button v-else class="btn btn-outline-info btn-sm w-100 shdaow">{{ options[index] }}</button>

                                    </td>
                                    <td style="padding: 10px;">
                                        <p v-html="question_active.question['option_'+answer]"></p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>

                    <div v-else>
                        <div class="alert alert-danger border-0 shadow">
                            <i class="fa fa-exclamation-triangle"></i> Soal Tidak Ditemukan!.
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div class="text-start">
                            <button type="button" class="btn btn-gray-400 btn-sm btn-block mb-2">Sebelumnya</button>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-gray-400 btn-sm">Selanjutnya</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card border-0 shadow">
                <div class="card-header text-center">
                    <div class="badge bg-success p-2"> {{ question_answered }} dikerjakan</div>
                </div>
                <div class="card-body" style="height: 330px;overflow-y: auto">

                    <div v-for="(question, index) in all_questions" :key="index">
                        <div width="20%" style="width: 20%; float: left;">
                            <div style="padding: 5px;">

                                <button v-if="index+1 == page"
                                    class="btn btn-gray-400 btn-sm w-100">{{ index + 1 }}</button>

                                <button v-if="index+1 != page && question.answer == 0"
                                    class="btn btn-outline-info btn-sm w-100">{{ index + 1 }}</button>

                                <button v-if="index+1 != page && question.answer != 0"
                                    class="btn btn-info btn-sm w-100">{{ index + 1 }}</button>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <button class="btn btn-danger btn-md border-0 shadow w-100">Akhiri Ujian</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    //import layout student
    import LayoutStudent from '../../../Layouts/Student.vue';

    //import Head and Link from Inertia
    import {
        Head,
        Link
    } from '@inertiajs/inertia-vue3';

    export default {
        //layout
        layout: LayoutStudent,

        //register components
        components: {
            Head,
            Link
        },

        //props
        props: {
            id: Number,
            page: Number,
            exam_group: Object,
            all_questions: Array,
            question_answered: Number,
            question_active: Object,
            answer_order: Array,
            duration: Object,
        },

        //composition API
        setup(props) {

            //define options for answer
            let options = ["A", "B", "C", "D", "E"];

            //return
            return {
                options,
            }

        }
    }

</script>

<style>

</style>
