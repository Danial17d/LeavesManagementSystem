<?php

namespace App\Enums;

namespace App\Enums;
enum RequestStatus: string
{
    case Submitted = "Submitted";
    case Approved = "Approved";
    case Rejected = "Rejected";
    case Cancelled = "Cancelled";
    case Pending = "Pending";
}

