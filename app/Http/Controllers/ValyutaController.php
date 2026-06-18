<?php
namespace App\Http\Controllers;
use App\Models\Valyuta;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ValyutaController extends Controller {
    public function index() {
        return view('malumotnamalar.valyutalar.index', ['valyutalar' => Valyuta::orderBy('asosiy','desc')->orderBy('kod')->get()]);
    }
    public function store(Request $request) {
        $d = $request->validate([
            'kod'       => 'required|string|max:10|unique:valyutalar,kod',
            'nomi'      => 'required|string|max:80',
            'belgi'     => 'nullable|string|max:10',
            'kurs'      => 'required|numeric|min:0',
            'kurs_sana' => 'nullable|date',
            'asosiy'    => 'nullable|boolean',
        ]);
        if (!empty($d['asosiy'])) Valyuta::query()->update(['asosiy' => false]);
        Valyuta::create($d);
        return back()->with('muvaffaqiyat', "Valyuta «{$d['kod']}» qo'shildi.");
    }
    public function update(Request $request, Valyuta $valyuta) {
        $d = $request->validate([
            'kod'       => ['required','string','max:10', Rule::unique('valyutalar','kod')->ignore($valyuta->id)],
            'nomi'      => 'required|string|max:80',
            'belgi'     => 'nullable|string|max:10',
            'kurs'      => 'required|numeric|min:0',
            'kurs_sana' => 'nullable|date',
            'asosiy'    => 'nullable|boolean',
            'holat'     => 'required|in:faol,nofaol',
        ]);
        if (!empty($d['asosiy'])) Valyuta::where('id','!=',$valyuta->id)->update(['asosiy' => false]);
        $d['asosiy'] = !empty($d['asosiy']);
        $valyuta->update($d);
        return back()->with('muvaffaqiyat', "Valyuta «{$valyuta->kod}» yangilandi.");
    }
    public function destroy(Valyuta $valyuta) {
        if ($valyuta->asosiy) return back()->with('xato', "Asosiy valyutani o'chirish mumkin emas.");
        $kod = $valyuta->kod; $valyuta->delete();
        return back()->with('muvaffaqiyat', "Valyuta «{$kod}» o'chirildi.");
    }
}
