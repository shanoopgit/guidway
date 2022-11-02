<?php

namespace App\Http\Controllers;
use App\Models\ShopDetail;
use App\Models\Address;
use App\Models\user;
use App\Models\AddHotel;
use App\Models\HotelImage;
use App\Models\Amenitie;

use Illuminate\Support\Str;



use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function AddPartner(Request $request)
    {
        $data =$request->all();

        $UserDAta=[
            'name'          => $data['username'],
            'email'   		=> $data['email'],  
            'mobile_number'	=>$data['mobile_number'],
            'password_1'	=>$data['password'],
            'password'=>bcrypt($data['password']),
            'user_type'		=>$data['user_type'],
            'status'		=>1,
                               
        ];
        $test1=new User;      
        $test1=User::create($UserDAta);

        $AddressData=[
            'user_id' 		=> $test1['id'],
            'name' 			=> $data['username'],
            'mobile_number' => $data['mobile_number'],  
            'district'		=>$data['district'],
            'state'			=>$data['state'],
            'landmark'		=>$data['landmark'],
            'pincode'		=>$data['pin'],
            'user_type'		=>$data['user_type'],
            'status'		=>1,          
        ];

        $test2=new Address;      
        $test2=Address::create($AddressData);

       
        $PartnerData=[
            'user_id' 			=> $test1['id'],
            'name'          	=> $data['name'],
            'register_number'   => $data['register_number'],  
            'place'				=>$data['Place'],
            'shop_type'		    =>$data['shoptype'],
            'address'			=>$data['address'],
            'area'				=>$data['area'],
            'email'				=>$data['email'],
            'person_name'		=>$data['Person'],
            'mobile_number'		=>$data['mobile_number'],
            'status'			=>1,

        ];
        $test1=new ShopDetail;      
    	$test1=ShopDetail::create($PartnerData);
          

        return redirect('/');

    }
    public function AddHotelPage(){

        $amenities= Amenitie::select('id','name')->where('status',1)->get();
        return view("add_hotel",compact('amenities')); 
    }
    public function ViewHotel(Request $request){
       $AddHotel= AddHotel::select('id','hotel_type','hotel_name','location_details','rate','amenities')
                    ->where('status',2)->get();
       return view("hotel",compact('AddHotel')); 
    }

     public function AddHotel(Request $request)
     {
        $data =$request->all();
          
        $encodedAmenities = json_encode($data['amenities']);
        $AddHotel =[

            'hotel_type'       => $data['hotel_type'],
            'hotel_name'       => $data['hotelname'],
            'location_details' => $data['location'],
            'rate'             => $data['amount'],
            'status'           =>1,
            'amenities'        => $encodedAmenities,

        ];
         $test3=new AddHotel;      
         $test3=AddHotel::create($AddHotel);


        $test4=new HotelImage;      

        $image_uploads = $request->file('hotel_image');
        foreach($image_uploads as $image_upload){

            $slug = str::slug($data['hotelname'],'-');
            $image_uploadname = $slug.rand().'.'.$image_upload->getClientOriginalExtension();
            $destinationPath = public_path('\images\hotels');
            $image_upload->move($destinationPath, $image_uploadname);

                
            $AddImage =[
                'add_hotel_id'     => $test3['id'],
                'status'           =>1,
                'image_name'     => $image_uploadname,
                'image_path'     =>$destinationPath,
            ];
            
            $test4=HotelImage::create($AddImage);   
        }
        $request->session()->flash('message','Successfully Inserted food Detailes');

        return view('hotel_admin_dashboard');
     }
}
