<?php

namespace App\Http\Controllers;

use Illuminate\Http\{Request, Response};
use Illuminate\Support\Facades\Validator;

class SSOController extends Controller
{
    public function getLMSLink(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'cod_curso' => 'required'
        ]);

        if ($validator->fails()) {
            // Si la validación falla, puedes retornar una respuesta con los errores
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $data = $request->only(['email', 'cod_curso']);
        $secret = env("SSO_SECRET");
        //$secret = "contraseña sso";

        $timestamp = time();
        //$timestamp = 1686668709;

        $identifier = urlencode($data['email']);
        //$identifier = "correo%40dominio.com";

        $cadena = "sso_identifier=$identifier&sso_timestamp=$timestamp&secret=$secret";
        //$cadena = "sso_identifier=correo%40dominio.com&sso_timestamp=1686668709&secret=contraseña ss";

        $hash_cadena = md5($cadena);
        //$hash_cadena = "3beea8cb022146a06c399245c2001dfd";

        $cadenaGET = "?sso_identifier=$identifier&sso_timestamp=$timestamp&sso_hash=$hash_cadena";
        //$cadenaGET="?sso_identifier=correo%40dominio.com&sso_timestamp=1686668709&sso_hash=3beea8cb022146a06c399245c2001dfd";

        if (isset($data['cod_curso'])) {
            $cadenaGET .= "&codcurso=" . $data['cod_curso'];

        }

        // $cadenaGET.="&codcurso=C23796";
        $sso = env('SSO_URL') . $cadenaGET;

        return response()->json([
            "done" => true,
            "sso" => $sso
        ]);
    }
}