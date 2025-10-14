<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;

class UserProgressExport implements FromView, WithTitle
{
    protected $data;
    protected $className;

    public function __construct($data, $className)
    {
        $this->data = $data;
        $this->className = $className;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        return view('exports.user-progress', [
            'data' => $this->data,
            'className' => $this->className
        ]);
    }

    /**
     * Set the sheet title
     */
    public function title(): string
    {
        return 'Progress Report';
    }
}
