<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HisobotController;
use App\Http\Controllers\MijozController;
use App\Http\Controllers\OmborController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\TransferHubController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\KassaTransferController;
use App\Http\Controllers\ContractReassignController;
use App\Http\Controllers\SupplierReturnController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\TovarGuruhController;
use App\Http\Controllers\TovarKatalogController;
use App\Http\Controllers\KirimController;
use App\Http\Controllers\ChiqimController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\RegKreditController;
use App\Http\Controllers\TulovController;
use App\Http\Controllers\TilController;
use App\Http\Controllers\TaminotchiController;
use App\Http\Controllers\VersionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\EmailNotificationController;
use App\Http\Controllers\HybridMailController;
use App\Http\Controllers\NotificationTemplateController;

// ─── Autentifikatsiya ─────────────────────────────────────────────

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    // throttle:5,1 = 5 urinish, 1 daqiqa bloklash (brute-force himoya)
    Route::post('/login', [AuthController::class, 'login'])
         ->middleware('throttle:5,1')
         ->name('login.post');
});

Route::middleware('auth')->group(function () {
    // POST — form submit orqali chiqish (asosiy yo'l)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // GET — brauzer URL orqali to'g'ridan /logout bosylsa ham ishlaydi
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');
    Route::get('/profil', [AuthController::class, 'profil'])->name('profil');
    Route::post('/profil/parol', [AuthController::class, 'parolOzgartirish'])->name('profil.parol');
});

// ─── Asosiy sahifalar (autentifikatsiya talab qilinadi) ───────────

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/statistika', [DashboardController::class, 'ajaxStatistika'])
        ->name('dashboard.statistika');

    // ─── Mijozlar ─────────────────────────────────────────────────
    Route::prefix('mijozlar')->name('mijozlar.')->group(function () {
        Route::get('/',           [MijozController::class, 'index'])->name('index');
        Route::get('/yangi',      [MijozController::class, 'create'])
            ->middleware('rol.check:admin,menejer')
            ->name('create');
        Route::post('/',          [MijozController::class, 'store'])->name('store');
        Route::get('/{mijoz}',    [MijozController::class, 'show'])->name('show');
        Route::get('/ajax-qidiruv',      [MijozController::class, 'ajaxQidiruv'])->name('ajax.qidiruv');
        Route::get('/{mijoz}/tahrirlash', [MijozController::class, 'edit'])
            ->middleware('rol.check:admin,menejer')
            ->name('edit');
        Route::put('/{mijoz}',    [MijozController::class, 'update'])->name('update');
    });

    // ─── Nasiya shartnomalar ──────────────────────────────────────
    Route::prefix('kreditlar')->name('kreditlar.')->group(function () {
        Route::get('/',          [RegKreditController::class, 'index'])->name('index');
        Route::get('/yangi',     [RegKreditController::class, 'create'])
            ->middleware('rol.check:admin,menejer')
            ->name('create');
        Route::post('/',         [RegKreditController::class, 'store'])
            ->middleware('rol.check:admin,menejer')
            ->name('store');
        Route::get('/{kredit}',  [RegKreditController::class, 'show'])->name('show');
        Route::get('/{kredit}/tahrirlash', [RegKreditController::class, 'edit'])
            ->middleware('rol.check:admin,menejer')
            ->name('edit');
        Route::put('/{kredit}',  [RegKreditController::class, 'update'])
            ->middleware('rol.check:admin,menejer')
            ->name('update');
        Route::get('/{kredit}/pdf', [RegKreditController::class, 'pdf'])->name('pdf');

        // To'lovlar
        Route::get('/{kredit}/tulov',          [TulovController::class, 'create'])
            ->middleware('rol.check:admin,menejer,kassir')
            ->name('tulov.create');
        Route::post('/{kredit}/tulov',         [TulovController::class, 'store'])
            ->middleware('rol.check:admin,menejer,kassir')
            ->name('tulov.store');
        Route::post('/{kredit}/oldin-tulov',   [TulovController::class, 'oldinStore'])
            ->middleware('rol.check:admin,menejer,kassir')
            ->name('tulov.oldin-store');
        Route::get('/{kredit}/qoldiq',         [TulovController::class, 'ajaxQoldiq'])
            ->name('tulov.qoldiq');
        Route::get('/{kredit}/tulov/{tulov}/kvitansiya', [TulovController::class, 'kvitansiya'])
            ->name('tulov.kvitansiya');
        Route::get('/{kredit}/tulov/{tulov}/tahrirlash', [TulovController::class, 'edit'])
            ->middleware('rol.check:admin,menejer')
            ->name('tulov.edit');
        Route::put('/{kredit}/tulov/{tulov}', [TulovController::class, 'update'])
            ->middleware('rol.check:admin,menejer')
            ->name('tulov.update');
        Route::delete('/{kredit}/tulov/{tulov}', [TulovController::class, 'destroy'])
            ->middleware('rol.check:admin')
            ->name('tulov.destroy');

        // Versiyalar
        Route::get('/{kredit}/versiyalar',          [VersionController::class, 'index'])->name('versiyalar.index');
        Route::get('/{kredit}/versiyalar/{versiya}', [VersionController::class, 'show'])->name('versiyalar.show');
    });

    // ─── Hisobotlar ───────────────────────────────────────────────
    Route::prefix('hisobotlar')->name('hisobotlar.')->group(function () {
        Route::get('/',                    [HisobotController::class, 'index'])->name('index');
        Route::get('/kelayotgan',          [HisobotController::class, 'kelayotganTulovlar'])->name('kelayotgan');
        Route::get('/kredit-portfolio',    [HisobotController::class, 'kreditPortfeli'])->name('kredit_portfolio');
        Route::get('/chiqarilgan',         [HisobotController::class, 'chiqarilganKreditlar'])->name('chiqarilgan');
        Route::get('/kechikish-analiz',    [HisobotController::class, 'kechikishAnaliz'])->name('kechikish_analiz');
        Route::get('/konstruktor',         [HisobotController::class, 'konstruktor'])->name('konstruktor');
        Route::post('/konstruktor',        [HisobotController::class, 'konstruktorHisobot'])->name('konstruktor.hisobot');
        Route::get('/excel/{tur}',         [HisobotController::class, 'excelExport'])->name('excel');
        Route::post('/konstruktor/excel',  [HisobotController::class, 'konstruktorExcel'])->name('konstruktor.excel');
        Route::get('/transferlar',            [HisobotController::class, 'transferHisobot'])->name('transfer');
    });

    // ─── Tovar katalog ────────────────────────────────────────────
    Route::prefix('katalog')->name('katalog.')->middleware('rol.check:admin,menejer')->group(function () {
        Route::get('/',               [TovarKatalogController::class, 'index'])->name('index');
        Route::get('/yangi',          [TovarKatalogController::class, 'create'])->name('create');
        Route::post('/',              [TovarKatalogController::class, 'store'])->name('store');
        Route::get('/{katalog}/edit', [TovarKatalogController::class, 'edit'])->name('edit');
        Route::put('/{katalog}',      [TovarKatalogController::class, 'update'])->name('update');
        Route::delete('/{katalog}',   [TovarKatalogController::class, 'destroy'])->name('destroy');
    });

    // ─── Tovar guruhlar ───────────────────────────────────────────
    Route::prefix('tovar-guruhlar')->name('tovar-guruhlar.')->middleware('rol.check:admin,menejer')->group(function () {
        Route::get('/',             [TovarGuruhController::class, 'index'])->name('index');
        Route::post('/',            [TovarGuruhController::class, 'store'])->name('store');
        Route::put('/{guruh}',      [TovarGuruhController::class, 'update'])->name('update');
        Route::delete('/{guruh}',   [TovarGuruhController::class, 'destroy'])->name('destroy');
    });

    // ─── Kirim ────────────────────────────────────────────────────
    Route::prefix('kirim')->name('kirim.')->middleware('rol.check:admin,menejer')->group(function () {
        Route::get('/',         [KirimController::class, 'index'])->name('index');
        Route::get('/yangi',    [KirimController::class, 'create'])->name('create');
        Route::post('/',        [KirimController::class, 'store'])->name('store');
        Route::get('/{kirim}',  [KirimController::class, 'show'])->name('show');
        Route::delete('/{kirim}', [KirimController::class, 'destroy'])->name('destroy');
    });

    // ─── Chiqim ───────────────────────────────────────────────────
    Route::prefix('chiqim')->name('chiqim.')->middleware('rol.check:admin,menejer')->group(function () {
        Route::get('/',          [ChiqimController::class, 'index'])->name('index');
        Route::get('/yangi',     [ChiqimController::class, 'create'])->name('create');
        Route::post('/',         [ChiqimController::class, 'store'])->name('store');
        Route::get('/{chiqim}',  [ChiqimController::class, 'show'])->name('show');
    });

    // ─── POS (Naqd savdo) ─────────────────────────────────────────
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/',              [PosController::class, 'index'])->name('index');
        Route::get('/tovarlar',      [PosController::class, 'tovarlar'])->name('tovarlar');
        Route::post('/saqlash',      [PosController::class, 'store'])->name('store');
        Route::get('/tarix',         [PosController::class, 'tarix'])->name('tarix');
        Route::get('/chek/{sotuv}',  [PosController::class, 'chekKorish'])->name('chek');
    });

    // ─── Ombor (eski, endi katalog ishlatiladi) ───────────────────
    Route::prefix('ombor')->name('ombor.')->group(function () {
        Route::get('/',       [OmborController::class, 'index'])->name('index');
        Route::get('/kirim',  fn() => redirect()->route('kirim.index'))->name('kirim');
        Route::get('/chiqim', fn() => redirect()->route('chiqim.index'))->name('chiqim');
    });

    // ─── Transferlar moduli (kengaytirilgan) ─────────────────────────
    Route::prefix('transfer')->name('transfer.')->middleware('auth')->group(function () {

        // Bosh sahifa va audit
        Route::get('/', [TransferHubController::class, 'index'])->name('index');
        Route::get('/audit', [TransferHubController::class, 'auditJurnal'])->name('audit');

        // Tovar transferlari (filiallar/omborlar arasi)
        Route::prefix('tovar')->name('tovar.')->middleware('rol.check:admin,menejer,omborchi')->group(function () {
            Route::get('/',                               [StockTransferController::class, 'index'])->name('index');
            Route::get('/yangi',                          [StockTransferController::class, 'create'])->name('create');
            Route::post('/',                              [StockTransferController::class, 'store'])->name('store');
            Route::get('/{transfer}',                     [StockTransferController::class, 'show'])->name('show');
            Route::post('/{transfer}/qabul',              [StockTransferController::class, 'qabulQilish'])->name('qabul');
            Route::post('/{transfer}/bekor',              [StockTransferController::class, 'bekorQilish'])->name('bekor');
        });

        // Kassa transferlari
        Route::prefix('kassa')->name('kassa.')->middleware('rol.check:admin,menejer,kassir')->group(function () {
            Route::get('/',                               [KassaTransferController::class, 'index'])->name('index');
            Route::get('/yangi',                          [KassaTransferController::class, 'create'])->name('create');
            Route::post('/',                              [KassaTransferController::class, 'store'])->name('store');
            Route::get('/{kassaTransfer}',                [KassaTransferController::class, 'show'])->name('show');
            Route::post('/{kassaTransfer}/qabul',         [KassaTransferController::class, 'qabulQilish'])->name('qabul');
            Route::post('/{kassaTransfer}/bekor',         [KassaTransferController::class, 'bekorQilish'])->name('bekor');
        });

        // Shartnoma qayta tayinlash va filial ko'chirish
        Route::prefix('shartnoma')->name('shartnoma.')->middleware('rol.check:admin,menejer')->group(function () {
            Route::get('/xodim-tarixi',                   [ContractReassignController::class, 'xodimIndex'])->name('xodim_tarixi');
            Route::post('/xodim-tayin',                   [ContractReassignController::class, 'xodimQaytaTayin'])->name('xodim_tayin');
            Route::get('/filial-tarixi',                  [ContractReassignController::class, 'filialIndex'])->name('filial_tarixi');
            Route::post('/filial-kochir',                 [ContractReassignController::class, 'filialKochirish'])->name('filial_kochir');
            // AJAX endpoints (kredit kartochkasidan)
            Route::post('/ajax/{kredit}/xodim-tayin',     [ContractReassignController::class, 'ajaxXodimTayin'])->name('ajax.xodim');
            Route::post('/ajax/{kredit}/filial-kochir',   [ContractReassignController::class, 'ajaxFilialKochir'])->name('ajax.filial');
        });

        // Ta'minotchiga qaytarish (Supplier Return)
        Route::prefix('supplier-return')->name('supplier-return.')->middleware('rol.check:admin,menejer,omborchi')->group(function () {
            Route::get('/',                               [SupplierReturnController::class, 'index'])->name('index');
            Route::get('/yangi',                          [SupplierReturnController::class, 'create'])->name('create');
            Route::post('/',                              [SupplierReturnController::class, 'store'])->name('store');
            Route::get('/{supplierReturn}',               [SupplierReturnController::class, 'show'])->name('show');
            Route::post('/{supplierReturn}/tasdiqlash',   [SupplierReturnController::class, 'tasdiqlash'])->name('tasdiqla');
            Route::post('/{supplierReturn}/qaytarildi',   [SupplierReturnController::class, 'qaytarildi'])->name('qaytarildi');
        });

        // To'lov turlari boshqaruvi
        Route::prefix('to-lov-turlari')->name('tolov_turi.')->middleware('rol.check:admin')->group(function () {
            Route::get('/',            [PaymentTypeController::class, 'index'])->name('index');
            Route::post('/',           [PaymentTypeController::class, 'store'])->name('store');
            Route::put('/{tulovTuri}', [PaymentTypeController::class, 'update'])->name('update');
            Route::post('/mapping',    [PaymentTypeController::class, 'mappingStore'])->name('mapping');
        });
    });

    // ─── Admin panel ──────────────────────────────────────────────
    Route::prefix('admin')->name('admin.')->middleware('rol.check:admin')->group(function () {
        Route::get('/',                  [AdminController::class, 'index'])->name('index');
        Route::get('/sozlamalar',        [AdminController::class, 'sozlamalar'])->name('sozlamalar');
        Route::post('/sozlamalar',       [AdminController::class, 'sozlamalarSaqla'])->name('sozlamalar.saqlash');
        Route::get('/ruxsatlar',         [AdminController::class, 'ruxsatlar'])->name('ruxsatlar');
        Route::post('/ruxsatlar',        [AdminController::class, 'ruxsatlarSaqla'])->name('ruxsatlar.saqlash');
        Route::get('/foydalanuvchilar',  [AdminController::class, 'foydalanuvchilar'])->name('foydalanuvchilar');
        Route::post('/foydalanuvchilar', [AdminController::class, 'foydalanuvchiStore'])->name('foydalanuvchilar.store');
        Route::post('/foydalanuvchilar/{foydalanuvchi}/holat', [AdminController::class, 'foydalanuvchiHolat'])->name('foydalanuvchilar.holat');
        Route::post('/foydalanuvchilar/{foydalanuvchi}/parol', [AdminController::class, 'foydalanuvchiParolReset'])->name('foydalanuvchilar.parol');
        Route::get('/audit',             [AuditController::class, 'index'])->name('audit');
        Route::get('/deploy',            [BackupController::class, 'deploy'])->name('deploy');
        Route::get('/deploy/db-zip',     [BackupController::class, 'dbZip'])->name('deploy.db');
        Route::get('/deploy/app-zip',    [BackupController::class, 'appZip'])->name('deploy.app');
        Route::get('/github',            [AdminController::class, 'github'])->name('github');
        // Xabarnoma sozlamalari (admin panel dan)
        Route::post('/notif/sms',      [SmsController::class, 'sozlamalarSaqla'])->name('notif.sms.saqlash');
        Route::post('/notif/telegram', [TelegramController::class, 'sozlamalarSaqla'])->name('notif.telegram.saqlash');
        Route::post('/notif/email',    [EmailNotificationController::class, 'sozlamalarSaqla'])->name('notif.email.saqlash');
        Route::post('/notif/hybrid',   [HybridMailController::class, 'sozlamalarSaqla'])->name('notif.hybrid.saqlash');
    });

    // ─── Ta'minotchilar moduli ─────────────────────────────────────
    Route::prefix('taminotchi')->name('taminotchi.')->middleware('auth')->group(function () {
        // Asosiy CRUD (menejer + admin + omborchi)
        Route::get('/',               [TaminotchiController::class, 'index'])->name('index');
        Route::get('/yangi',          [TaminotchiController::class, 'create'])->name('create')
            ->middleware('rol.check:admin,menejer');
        Route::post('/',              [TaminotchiController::class, 'store'])->name('store')
            ->middleware('rol.check:admin,menejer');
        Route::get('/{taminotchi}',   [TaminotchiController::class, 'show'])->name('show');
        Route::get('/{taminotchi}/edit', [TaminotchiController::class, 'edit'])->name('edit')
            ->middleware('rol.check:admin,menejer');
        Route::put('/{taminotchi}',   [TaminotchiController::class, 'update'])->name('update')
            ->middleware('rol.check:admin,menejer');

        // Kirim (omborchi + admin + menejer)
        Route::get('/{taminotchi}/kirim/yangi', [TaminotchiController::class, 'kirimCreate'])->name('kirim.create')
            ->middleware('rol.check:admin,menejer,omborchi');
        Route::post('/{taminotchi}/kirim',      [TaminotchiController::class, 'kirimStore'])->name('kirim.store')
            ->middleware('rol.check:admin,menejer,omborchi');

        // To'lov (kassir + admin + menejer)
        Route::post('/{taminotchi}/tulov', [TaminotchiController::class, 'tulovStore'])->name('tulov.store')
            ->middleware('rol.check:admin,menejer,kassir');

        // Akt sverka va hisobotlar (barcha login bo'lganlar)
        Route::get('/{taminotchi}/akt-sverka', [TaminotchiController::class, 'aktSverka'])->name('akt_sverka');
        Route::get('/hisobot/reestr',           [TaminotchiController::class, 'tulovReestr'])->name('tulov_reestr');
        Route::get('/hisobot/balans',           [TaminotchiController::class, 'hisobot'])->name('hisobot');
    });

    // Til o'zgartirish
    Route::post('/til', [TilController::class, 'ozgartir'])->name('til.ozgartir');

    // ─── Xabarnoma moduli ─────────────────────────────────────────
    Route::prefix('xabarnoma')->name('xabarnoma.')->middleware('auth')->group(function () {

        // SMS
        Route::prefix('sms')->name('sms.')->middleware('rol.check:admin,menejer')->group(function () {
            Route::get('/',            [SmsController::class, 'guruhli'])->name('index');
            Route::get('/guruhli',     [SmsController::class, 'guruhli'])->name('guruhli');
            Route::post('/guruhli',    [SmsController::class, 'guruhliSend'])->name('guruhli.send');
            Route::post('/preview',    [SmsController::class, 'preview'])->name('preview');
            Route::get('/yakka',       [SmsController::class, 'yakka'])->name('yakka');
            Route::post('/yakka',      [SmsController::class, 'yakkaSend'])->name('yakka.send');
            Route::get('/tarix',       [SmsController::class, 'tarix'])->name('tarix');
            Route::get('/sozlamalar',  [SmsController::class, 'sozlamalar'])->name('sozlamalar');
            Route::post('/sozlamalar', [SmsController::class, 'sozlamalarSaqla'])->name('sozlamalar.saqlash');
            Route::post('/test',       [SmsController::class, 'testSms'])->name('test');
        });

        // Telegram
        Route::prefix('telegram')->name('telegram.')->middleware('rol.check:admin,menejer')->group(function () {
            Route::get('/',            [TelegramController::class, 'index'])->name('index');
            Route::post('/sozlamalar', [TelegramController::class, 'sozlamalarSaqla'])->name('sozlamalar.saqlash');
            Route::post('/test',       [TelegramController::class, 'testTelegram'])->name('test');
        });

        // Email
        Route::prefix('email')->name('email.')->middleware('rol.check:admin,menejer')->group(function () {
            Route::get('/',            [EmailNotificationController::class, 'index'])->name('index');
            Route::post('/sozlamalar', [EmailNotificationController::class, 'sozlamalarSaqla'])->name('sozlamalar.saqlash');
            Route::post('/test',       [EmailNotificationController::class, 'testEmail'])->name('test');
        });

        // Gibrid Pochta
        Route::prefix('hybrid-mail')->name('hybrid_mail.')->middleware('rol.check:admin,menejer')->group(function () {
            Route::get('/',            [HybridMailController::class, 'index'])->name('index');
            Route::post('/sozlamalar', [HybridMailController::class, 'sozlamalarSaqla'])->name('sozlamalar.saqlash');
            Route::post('/test',       [HybridMailController::class, 'testSend'])->name('test');
        });

        // Shablonlar
        Route::prefix('shablonlar')->name('shablonlar.')->middleware('rol.check:admin,menejer')->group(function () {
            Route::get('/',                  [NotificationTemplateController::class, 'index'])->name('index');
            Route::get('/yangi',             [NotificationTemplateController::class, 'create'])->name('create');
            Route::post('/',                 [NotificationTemplateController::class, 'store'])->name('store');
            Route::get('/{shablon}/tahrir',  [NotificationTemplateController::class, 'edit'])->name('edit');
            Route::put('/{shablon}',         [NotificationTemplateController::class, 'update'])->name('update');
            Route::post('/{shablon}/preview',[NotificationTemplateController::class, 'preview'])->name('preview');
        });
    });


});