<?php

namespace App\Http\Controllers;

use App\Models\ComboProduct;
use App\Models\ComboProductDetail;
use App\Models\Voucher;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ComboProductController extends Controller
{

    public function getVoucherById(string $id){
        try{
            $voucher = Voucher::find($id);
            if(!$voucher){
                throw new Error("cannot found this voucher");
            }
            return response()->json(["message"=>"voucher by id ".$id,"data"=>$voucher],200);
        }
        catch(\Exception $e){
          return response()->json(["error"=>"voucher by id ".$id,"data"=>$e->getMessage()],500);
        }
    }

    // input list_product container array id product and
    //  list quantity container int quantity of product
    public function create_combo_product(Request $request){
        DB::beginTransaction();
        try {
            $data = $request->all();
            $access_url_img = null;

            //create image
            if($request->has('image')){
                $path = $request->file('image')->store('combo_product_img','public');
                $access_url_img = asset("storage/".$path);
            }
            
            //create combo-product
            $combo_product = new ComboProduct();
            $combo_product->name = $data['name'];
            $combo_product->url_img = $access_url_img;
            $combo_product->description = $data['description'];
            $combo_product->price = $data['price'];
            $combo_product->quantity = $data['quantity'];
            $combo_product->save();

            if(!$request->has('list_product')){
                throw new Error("Please check field list_product, it is empty now");
            }
            //create list combo_product_detail
            $listProduct =  collect();
            for($i = 0;$i<count($data['list_product']);$i++){
                try {
                    $detail = new ComboProductDetail();
                    $detail->product_id = $data['list_product'][$i];
                    $detail->combo_product_id = $combo_product->id;
                    if(!isset($data['quantity'][$i])){
                        throw new Error("not exist quantity product $i",404);
                    }
                    $detail->quantity = $data['quantity'][$i];
                    $detail->save();
                    $listProduct->push($detail);
                } catch (\Throwable $th) {
                    throw new Error($th,500);
                }
            }
            // create category_combo_product_detail 
            $listCategory = $data['category'];
            $listCategories = collect();
            foreach($listCategory as $category){
                $categoryComboProductDetail = new CategoryComboProductController();
                $result = $categoryComboProductDetail->create_category_combo_product($combo_product->id,$category);
                if($result instanceof \Throwable) throw new Error($result->getMessage());
                $listCategories->push($category);
            }

            $combo_product->listCategories = $listCategories;
            $combo_product->listProducts = $listProduct;

            DB::commit();
            return response()->json(["combo-product"=>$combo_product],200);
        } catch (\Throwable $th) {
            DB::rollBack();
            if($access_url_img) Storage::delete("public/combo_product_img/".basename($access_url_img));
            return response()->json(["error"=>$th->getMessage()],500);
        }
    }

    public function findComboProductById(String $id_product){
        try {
            $combo_product = ComboProduct::find($id_product);
            $combo_product->comboProductDetails;
            if($combo_product) return response()->json(["message"=>"combo product by id ".$id_product,
            "combo-product"=>$combo_product],200);
            else throw new Error("Not found this product");
        } catch (\Throwable $th) {
           return response()->json(["error"=>"product by id ".$id_product,"data"=>$th->getMessage()],500);
        }
    }

    public function getAll(){
        try {
            $combo_product = ComboProduct::all();
            $listCP = collect();
            for($i = 0;$i<count($combo_product);$i++){
                $combo = ComboProduct::find($combo_product[$i]['id']);
                $combo->comboProductDetails;
                $listCP->push($combo);
            }
            if($combo_product) return response()->json(["message"=>"combo product find all ","data"=>$listCP],200);
            else throw new Error("Not found this product");
        } catch (\Throwable $th) {
           return response()->json(["error"=>"product by id ","data"=>$th->getMessage()],500);
        }
    }

    public function update_combo_product(Request $request,String $id_product){
        DB::beginTransaction();
        $combo_product = ComboProduct::find($id_product);
        if(!$request->has('list_product')){
                throw new Error("Please check field list_product, it is empty now");
            }
        if(!$combo_product) return response()->json(["message"=>"not found this product"],404);
        $data = $request->all();
        $path_access = $data['image'];
        if($request->hasFile('image')){
            $fileName = basename($combo_product->url_img);
            if(Storage::exists("public/combo_product_img/".$fileName)){
                Storage::delete("public/combo_product_img/".$fileName);
                $path = $data['image']->store('combo_product_img','public');
                $path_access = asset('storage/'.$path);
                $combo_product->url_img = $path_access;
            }else echo ("khong ton tai img");
        }
        
        try {
            $combo_product->update([
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'quantity'=>$data['quantity'],
                'url_img'=>$path_access,
            ]);
            // delete full combo_product_detail old
            ComboProductDetail::where('combo_product_id',$combo_product['id'])->delete();
            //create new list combo_product_detail
            $listProduct =  collect();
            for($i = 0;$i<count($data['list_product']);$i++){
                try {
                    $detail = new ComboProductDetail();
                    $detail->product_id = $data['list_product'][$i];
                    $detail->combo_product_id = $combo_product->id;
                    if(!isset($data['quantity'][$i])){
                        throw new Error("not exist quantity product $i",404);
                    }
                    $detail->quantity = $data['quantity'][$i];
                    $detail->save();
                    $listProduct->push($detail);
                } catch (\Throwable $th) {
                    throw new Error($th,500);
                }
            }
            
            // update category_combo_product_detail
            $listCategory_id_new = $data['category'];
            // find category in dbs
            $listCategory_old = DB::table('category_combo_product_detail')->where('combo_product_id',$id_product)->get();
            $listCategory_id_old = [];
            foreach($listCategory_old as $category){
                $listCategory_id_old[] = $category->category_id;
            }
            // handle update category_combo_product_detail
            $list_category_id_only_new = array_diff($listCategory_id_new,$listCategory_id_old);
            $list_category_id_only_old = array_diff($listCategory_id_old,$listCategory_id_new);
            foreach($list_category_id_only_new as $category_id){
                $categoryComboProductDetail = new CategoryComboProductController();
                $result = $categoryComboProductDetail->create_category_combo_product($combo_product->id,$category_id);
                if($result instanceof \Throwable) throw new Error($result->getMessage());
            }

            foreach($list_category_id_only_old as $category_id){
                $categoryComboProductDetail = new CategoryComboProductController();
                $result = $categoryComboProductDetail->delete_category_combo_product($combo_product->id,$category_id);
                if($result instanceof \Throwable) throw new Error($result->getMessage());
            }

            //handle update success
            if($combo_product->wasChanged())  
            {
                DB::commit();
                return Response()->json(['message'=>"update successfully","data"=>$combo_product],200);
            }
            //handle update fail
            else return Response()->json(['message'=>"Error, make sure input not mistake field",],200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Response()->json(['message'=>"Error, make sure input not mistake field","error"=>$th->getMessage()],500);
        }
    }

    public function deleteComboProductById(String $id_product){
        try {
            // find product
            $combo_product = ComboProduct::find($id_product);
            if(!$combo_product) throw new Error( "Not found product this product",404);
            //get name img product
            $img_access = basename($combo_product->url_img);
            Storage::delete("public/product_img/".$img_access);
            // check exist or not to confirm image product was deleted 
            if(Storage::exists("public/product_img/".$img_access)) throw new Error("Delete image product faild",500);
            
            DB::table("combo_product_detail")->where("combo_product_id",$id_product)->delete();
            DB::table("category_combo_product_detail")->where("combo_product_id",$id_product)->delete();
            DB::table('order_combo_product_detail')->where('combo_product_id',$id_product)->delete();
            $combo_product->delete();
            return response()->json(["message"=>"delete successfully"],200);
        } catch (\Throwable $th) {
             return Response()->json(["Error"=>$th->getMessage()],$th->getCode());
        } 
    }
}