<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sched;

class PerfTrack extends Component
{
    use WithPagination;
    public $search = '';

    public $perPage = 5;

    public $shift = '';
    
    public function render()
    {
        return view('livewire.schedule',
        [
            'scheds' => Sched::search($this->search)
            ->when($this->shift !== '',function($query){
                $query->where('shift',$this->shift);
            })
            ->paginate($this->perPage)
        ]
        );
    }
}
