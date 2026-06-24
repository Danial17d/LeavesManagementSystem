<?php

namespace App\Enums;

enum RequestStatus: string
{
    case Submitted = 'submitted';
    case Approved  = 'approved';
    case Revoked   = 'revoked';
    case Rejected  = 'rejected';
    case Cancelled = 'cancelled';
    case Pending   = 'pending';
}

