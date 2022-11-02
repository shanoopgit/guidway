<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use session;

use App\Models\User;
use App\Models\Address;
use App\Models\ShopDetail;
use App\Models\UserType;
use App\Models\AddHotel;


class UserController extends Controller
{
    public function AddUser(Request $request)
    {
        $data =$request->all();

        $UserDAta=[
            'name'          => $data['username'],
            'email'    => $data['email'],  
            'mobile_number'=>$data['mobile_number'],
            'password_1'=>$data['password'],
            'password'=>bcrypt($data['password']),
            'user_type'=>3,
            'status'=>1,
                               
        ];

        $test1=new User;      
        $test1=User::create($UserDAta);

         $AddressData=[
            'user_id'       => $test1['id'],

            'name' => $data['username'],
             'mobile_number'    => $data['state'],  
            'district'=>$data['district'],
            'state'=>$data['state'],
            'landmark'=>$data['landmark'],
            'pincode'=>$data['pin'],
            'user_type'=>3,
            'status'=>1,
            
                    
        ];

        $test2=new Address;      
        $test2=Address::create($AddressData);

        return redirect('/');

    }
    public function Signin(Request $request)
    {

        $input = ['email'=> request('email'),'password'=> request('password')];

        if(auth()->attempt($input)){
            session()->put('user',auth()->user());

            if(auth()->user()->user_type==1){

                    return redirect('/admin/home');

                

           }else if(auth()->user()->user_type==3){
                return redirect('/');

           }else if(auth()->user()->user_type==4){


                return view('hotel_admin_dashboard');
           }

        }else{
            return redirect('register')->with('danger','Login is Invalid');
        }
    }

    public function AdminHome(){

            $totalusers = User::count();
            $totalpartners = ShopDetail::count();
            $totalhotels = ShopDetail::whereIn('shop_type', array('4-1','4-2','4-3'))->count();


            return view('Admin/admin_dashboard', compact('totalusers','totalpartners','totalhotels'));
            
    }

    public function AdminHotels(){

            
             $AddHotel= AddHotel::select('id','hotel_type','hotel_name','location_details','rate','amenities','status')->get();
                    
            return view('Admin/admin_hotels', compact('AddHotel'));


    }

    public function AdminUsers(){

             $Hotels = ShopDetail::from(with(new ShopDetail)->getTable())  
             ->join(with(new User)->getTable(). ' as b', 'b.id','shop_details.user_id')
             ->join(with(new UserType)->getTable(). ' as c', 'c.id','b.user_type')
             ->select('shop_details.*','b.user_type','c.name as user_type_name')
             ->get();
            
                    
            return view('Admin/admin_users', compact('Hotels'));


    }

    public function UpdateHotelStatusApprove($id){

        $hotel=AddHotel::find($id);
        $hotel->status= 2;
        $hotel->save();      
        return redirect('admin/hotels');
    }

    public function UpdateHotelStatusReject($id){

        $hotel=AddHotel::find($id);
        $hotel->status= 1;
        $hotel->save();      
        return redirect('admin/hotels');
    }

    public function Logout(){
        
        auth()->logout();
       session()->forget(['user']);


         return redirect('/');

    }

}
