use App\Http\Controllers\GajiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GajiController.class, 'index'])->name('kalkulator.index');
Route::get('/history', [GajiController.class, 'history'])->name('kalkulator.history');
Route::post('/update-salary', [GajiController.class, 'updateSalary'])->name('salary.update');
Route::post('/add-expense', [GajiController.class, 'addExpense'])->name('expense.add');
