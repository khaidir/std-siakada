<?php

namespace App\Enums;

enum ThesisStatus: string
{
    case Proposal = 'proposal';
    case SeminarProposal = 'seminar_proposal';
    case Sidang = 'sidang';
    case Lulus = 'lulus';
    case Revisi = 'revisi';
}
