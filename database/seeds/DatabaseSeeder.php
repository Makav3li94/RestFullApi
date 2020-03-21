<?php
use App\User;
use App\Category;
use App\Product;
use App\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        // $this->call(UsersTableSeeder::class);
        User::truncate();
        Category::truncate();
        Product::truncate();
        Transaction::truncate();
        DB::table('category_product')->truncate();

	    User::flushEventListeners();
	    Category::flushEventListeners();
	    Product::flushEventListeners();
	    Transaction::flushEventListeners();

        $usersQty =1000;
        $catsQty =30;
        $productQty =1000;
        $transQty =200;

        factory(User::class,$usersQty)->create();
        factory(Category::class,$catsQty)->create();

        factory(Product::class,$productQty)->create()->each(
            function($product){
                $categories = Category::all()->random(mt_rand(1,5))->pluck('id');
                $product->categories()->attach($categories);
            }
        );
        factory(Transaction::class,$transQty)->create();
    }   
}
