<?php

namespace App\Http\Controllers;

use App\Http\Requests\SmsTemplateRequest;
use App\Models\Notification;
use App\Models\SmsTemplate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function index()
    {
        $tenant = tenant();
        $notifications = Notification::where('tenant_id', tenant('id'))
            ->paginate(25);

        return view('admin.sms.index', [
            'tenant' => $tenant,
            'notifications' => $notifications
        ]);
    }

    public function update(Request $request)
    {
        $tenant = tenant();
        $tenant->sms_status = $request->sms_status;
        $tenant->sms_send_hour = $request->sms_send_hour;
        $tenant->save();
        return redirect()->back()->with('success', 'Opções SMS atualizadas com sucesso.');
    }

    public function templatesList()
    {
        $templates = SmsTemplate::paginate(15);
        return view('admin.sms.templates_list', compact('templates'));
    }

    public function templatesForm()
    {
        $template = new SmsTemplate();
        return view('admin.sms.templates_form', compact('template'));
    }

    public function templatesStore(SmsTemplateRequest $request)
    {
        $template = new SmsTemplate();
        $template->fill($request->all());
        $template->save();
        return redirect()->route('sms.templates.list')->with('success', 'Template de SMS criado com sucesso.');
    }

    public function templatesUpdate(SmsTemplateRequest $request, $id)
    {
        $template = SmsTemplate::findOrFail($id);
        $template->fill($request->all());
        $template->save();
        return redirect()->route('sms.templates.edit', ['id' => $template->id])->with('success', 'Template de SMS atualizado com sucesso.');
    }

    public function templatesEdit($id)
    {
        $template = SmsTemplate::findOrFail($id);
        return view('admin.sms.templates_form', compact('template'));
    }

    public function templatesDestroy($id)
    {
        $template = SmsTemplate::findOrFail($id);
        $template->delete();
        return redirect()->route('sms.templates.list')->with('success', 'Template de SMS excluído com sucesso.');
    }
}
