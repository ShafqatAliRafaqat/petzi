<?php

namespace App\Exports;

use App\Models\Admin\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ByStatusExport implements FromCollection,WithHeadings,WithEvents,ShouldAutoSize
{
    use Exportable;
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $request    =   $this->request;
        $status_id  =   $request->status_id;
        $start      =   $request->start_date;
        $end        =   $request->end_date;
        // dd($status_id);
        // dd($start,$end);
        if (!$start && !$end) {
        $start      =   Carbon::now()->subyear(1)->startOfYear();
        $end        =   Carbon::now();
        }
        $customers  =   Customer::where('status_id',$status_id)
                    ->Where(function ($q) use($start,$end){
                        $q->orWhereBetween('updated_at', [$start, $end])
                        ->orwhereBetween('created_at', array($start, $end));
                    })
                    ->select('id','name','phone','address','patient_coordinator_id','status_id','notes')
                    ->get();
                    // dd($customers);
        $statuses   =   DB::table('status')->get();
        foreach ($customers as $customer) {
            // Gender ID Matching
            // $customer['gender']         =   ($customer->gender == 1) ? 'Female' : 'Male';
            // Status ID Matching and returning names of it
            foreach ($statuses as $status) {
                if($status->id == $customer['status_id']){
                    $customer['status_id']     =   $status->name;
                }
            }
            $users      =   Auth::user()->find($customer->patient_coordinator_id);
            $customer['patient_coordinator_id']     =   $users->name;
            // $customer['marital_status']             =   ($customer->marital_status == 1) ? 'Married' : 'Unmarried';

            $customer['notes']                      =   strip_tags($customer->notes);
            $customer_details                       =   TreatmentsAndCenters($customer->id);
            $customer['procedures']                 =   $customer_details->treatment;
            $customer['centers']                    =   $customer_details->center_name;
            $customer['costs']                      =   $customer_details->cost;

            // $customer['created_at']                 =   $customer->created_at->format('Y-m-d');
            // $customer['updated_at']                 =   $customer->updated_at->format('Y-m-d');
        }
        // dd($customers);
        return $customers;

        // return Customer::all();
    }
    public function headings(): array
    {
        return [
            'ID',
            // 'Customer ID',
            'Name',
            // 'Email',
            'Phone',
            'Address',
            // 'Gender',
            // 'Marital Status',
            // 'Age',
            // 'Weight',
            // 'Height',
            'Patient Owner',
            'Status',
            'Notes',
            // 'Follow Up',
            // 'Created at',
            // 'Updated at',
            'Procedures',
            'Centers',
            'Costs'

        ];
    }

     /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }

}
