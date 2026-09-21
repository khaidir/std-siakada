<?php

namespace App\Repositories\Eloquent;

use App\Enums\StudyPlanDetailStatus;
use App\Models\StudyPlanDetail;
use App\Repositories\Contracts\StudyPlanDetailRepository as StudyPlanDetailRepositoryContract;
use Illuminate\Support\Collection;

class StudyPlanDetailRepository implements StudyPlanDetailRepositoryContract
{
    public function listApprovedByOffering(int $offeringId): Collection
    {
        return StudyPlanDetail::query()
            ->select(['id', 'study_plan_id', 'course_offering_id'])
            ->with([
                'studyPlan:id,student_id',
                'studyPlan.student:id,user_id,nim',
                'studyPlan.student.user:id,name',
                'grade:id,study_plan_detail_id,assignment_score,midterm_score,final_score,score,letter_grade,grade_point',
            ])
            ->where('course_offering_id', $offeringId)
            ->where('status', StudyPlanDetailStatus::Approved)
            ->orderBy('id')
            ->get();
    }

    public function approvedForOffering(int $offeringId, array $detailIds): Collection
    {
        return StudyPlanDetail::query()
            ->select(['id', 'study_plan_id', 'course_offering_id'])
            ->with('studyPlan:id,student_id')
            ->where('course_offering_id', $offeringId)
            ->where('status', StudyPlanDetailStatus::Approved)
            ->whereIn('id', $detailIds)
            ->get();
    }

    public function listByPlan(int $planId): Collection
    {
        return StudyPlanDetail::query()
            ->select(['id', 'study_plan_id', 'course_offering_id', 'status', 'created_at'])
            ->with([
                'courseOffering:id,course_id,lecturer_id,day,start_time,end_time',
                'courseOffering.course:id,code,name,sks',
                'courseOffering.lecturer:id,user_id',
                'courseOffering.lecturer.user:id,name',
            ])
            ->where('study_plan_id', $planId)
            ->orderBy('id')
            ->get();
    }

    public function add(array $data): StudyPlanDetail
    {
        return StudyPlanDetail::query()->create($data);
    }

    public function remove(int $detailId): void
    {
        StudyPlanDetail::where('id', $detailId)->delete();
    }

    public function findById(int $detailId): ?StudyPlanDetail
    {
        return StudyPlanDetail::query()
            ->select(['id', 'study_plan_id', 'course_offering_id', 'status'])
            ->where('id', $detailId)
            ->first();
    }
}
