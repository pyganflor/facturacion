Estimado {{$usuario['razon_social']}}, su firma electrónica cargada en el sistema de facturación electrónica de Dasalflor S.A,
está por vencerse en fecha {{\Carbon\Carbon::parse($usuario['firma_hasta'])->format('d/m/Y')}},
debe actualizar la misma antes de su fecha de vencimiento para poder seguir usando el servicio.