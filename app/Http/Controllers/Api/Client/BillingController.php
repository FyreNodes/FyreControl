<?php

namespace Pterodactyl\Http\Controllers\Api\Client;

use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\AlertsMessageBag;
use Pterodactyl\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use JavaScript;
use Pterodactyl\Repositories\Eloquent\NestRepository;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Support\Facades\Session;

class BillingController extends Controller
{

    private $alert;

    /**
     * @var \Illuminate\Contracts\Encryption\Encrypter
     */
    private $encrypter;

    public function __construct(AlertsMessageBag $alert, Encrypter $encrypter)
    {
        $this->alert = $alert;
        $this->encrypter = $encrypter;
    }

    public function getCategories(Request $request)
    {
        $categories = DB::table('shop_categories')->get();
        return ['success' => true, 'data' => $categories];
    }

    public function getProducts(Request $request, $id)
    {
        $products = DB::table('products')->get();
        return ['success' => true, 'data' => $products];
    }

    public function getShoppingCart(Request $request)
    {
        $cart = DB::table('shop_cart')->where('session_id', '=', $_COOKIE['remember_web_59ba36addc2b2f9401580f014c7f58ea4e30989d'])->get();
        $amount = 0;
        $price = 0;

        foreach ($cart as $key => $cartitem) {
            $product = DB::table('shop_products')->where("id", '=', intval($cartitem->product_id))->first();
            if (!$product) {
                unset($cart[$key]);
            }
            if (isset($cart[$key])) {
                $cart[$key]->name = $product->name;
                $category = DB::table('shop_categories')->where("id", '=', intval($product->category_id))->first();
                if (!$category) {
                    unset($cart[$key]);
                }
                $cart[$key]->category = $category->name;
                $cart[$key]->price = $cartitem->amount*$product->price;
                $amount += intval($cartitem->amount);
                $price += $cartitem->amount*$product->price;
            }
        }

        return ['success' => true, 'data' => ['cart' => $cart, 'totalamount' => $amount, 'totalprice' => $price]];
    }

    public function addToShoppingCart(Request $request)
    {
        $this->validate($request, [
            'pid' => 'required',
            'amount' => 'required'
        ]);

        $already_exists = DB::table('shop_cart')->where('session_id', '=', $_COOKIE['remember_web_59ba36addc2b2f9401580f014c7f58ea4e30989d'])->where('product_id', '=', (int) $request->input("pid"))->get();

        if (count($already_exists) == 0) {
            DB::table('shop_cart')->insert([
                'session_id' => $_COOKIE['remember_web_59ba36addc2b2f9401580f014c7f58ea4e30989d'],
                'product_id' => (int) $request->input("pid"),
                'amount' => (int) $request->input("amount")
            ]);
        } else {
            if ($already_exists[0]->amount + (int) $request->input("amount") == 0) {
                DB::table('shop_cart')->where('session_id', '=', $_COOKIE['remember_web_59ba36addc2b2f9401580f014c7f58ea4e30989d'])->where('product_id', '=', (int) $request->input("pid"))->delete();
            } else {
                DB::table('shop_cart')->where('session_id', '=', $_COOKIE['remember_web_59ba36addc2b2f9401580f014c7f58ea4e30989d'])->where('product_id', '=', (int) $request->input("pid"))->update([
                    'amount' => $already_exists[0]->amount + (int) $request->input("amount")
                ]);
            }
        }
        return ['success' => true];
    }
}
