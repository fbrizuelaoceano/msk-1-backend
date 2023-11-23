<?php

namespace App\Services;

use App\Models\ProductCRM;

class ZohoContactsService extends ZohoService
{
    protected string $module = 'contacts';

    public function enrollCourse($contact, $request){
        $contactCRM = $this->getBy($contact->entity_id_crm);
        $contactProgress = $contact->courses_progress()->where("Product_Code", $request->product_code)->where("Estado_cursada", "Sin enrolar");
        $product = ProductCRM::where('product_code', $request->product_code)->first();
        $productId = $product->entity_id;

        $coursesForm = collect($contactCRM["data"][0]["Formulario_de_cursada"]);

        foreach ($coursesForm as $cf) {
            // Si el ID del curso coincide con el ID del producto
            if ($cf['Nombre_de_curso']['id'] == $productId) {
                // Actualizamos el estado de la cursada
                $cf['Estado_cursada'] = 'Listo para enrolar';
                $contactProgress->update(["Estado_cursada" => 'Listo para enrolar']);
            }
        }

        return $this->updateField($contact->entity_id_crm,'Formulario_de_cursada', $coursesForm->toArray());

    }

    public function updateField($id,$field, $fieldData){
        $data = array($field => $fieldData);

      return $this->put($id,$data);
    }
}
