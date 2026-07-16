<?php

/*
|--------------------------------------------------------------------------
| Tours de onboarding
|--------------------------------------------------------------------------
|
| Cada clave es el nombre de una ruta (Route::currentRouteName()). El valor
| es la lista ordenada de pasos del tour para esa página: cada paso oscurece
| toda la pantalla, resalta el elemento correspondiente (si el paso tiene
| "selector") y muestra una tarjeta con "titulo" y "texto".
|
| Para agregar el onboarding a una vista nueva, basta con incluir aquí una
| entrada con el nombre de esa ruta y sus pasos. No es necesario modificar
| el motor (app/Http/Controllers/OnboardingController.php,
| resources/js/onboarding.js, resources/views/components/onboarding.blade.php)
| ni marcar nada en el blade de la vista, salvo que se quiera resaltar un
| elemento puntual (en ese caso, agregar data-onboarding="clave" a ese
| elemento y usar el mismo valor como "selector": '[data-onboarding="clave"]').
|
*/

return [

    'administrador.dashboard' => [
        [
            'titulo'   => 'Bienvenido a su panel',
            'texto'    => 'Este es su panel de administrador, el punto central para controlar su negocio. Desde el menú de la izquierda puede acceder a las secciones de Sucursales, Inventario, Catálogo, Garantías, Pedidos y Cajas, además de la configuración general del negocio.',
            'selector' => '[data-onboarding="sidebar-nav"]',
            'placement' => 'right',
        ],
        [
            'titulo'   => 'Seleccione el periodo',
            'texto'    => 'En la parte superior puede cambiar entre Hoy, Semana, Mes o definir un rango de fechas personalizado. Todas las estadísticas y gráficas de este panel se actualizan automáticamente según el periodo que elija.',
            'selector' => '[data-onboarding="admin-periodo"]',
            'placement' => 'bottom',
        ],
        [
            'titulo'   => 'Filtre por sucursal',
            'texto'    => 'Si su negocio tiene más de una sucursal, puede consultar las estadísticas combinadas de todas ellas, o enfocarse en una sola sucursal en particular para revisar su desempeño de forma individual.',
            'selector' => '[data-onboarding="admin-sucursal-selector"]',
            'placement' => 'bottom',
        ],
        [
            'titulo'   => 'Resumen de su negocio',
            'texto'    => 'Más abajo encontrará los ingresos totales, el número de ventas, el ticket promedio y cómo se distribuyen sus ingresos por método de pago (efectivo, tarjeta, transferencia) y por origen: ventas de bicicletas, reparaciones o piezas sueltas.',
            'selector' => '[data-onboarding="admin-resumen"]',
            'placement' => 'top',
        ],
        [
            'titulo'   => 'Gráficas detalladas',
            'texto'    => 'Al final de la página puede seleccionar cualquiera de las tarjetas de gráficas para ver el detalle de ingresos, ventas o el desempeño de cada sucursal a lo largo del tiempo, comparándolo con periodos anteriores.',
            'selector' => '[data-onboarding="admin-graficas"]',
            'placement' => 'top',
        ],
    ],

    'stock.index' => [
        [
            'titulo'   => 'Bienvenido a su inventario',
            'texto'    => 'Aquí puede consultar todas las bicicletas asignadas a su sucursal, junto con su modelo, color, número de serie y estado actual (disponible, vendida o en reparación). Use esta vista como punto de partida para revisar su stock diario.',
            'selector' => '[data-onboarding="vendedor-tabla"]',
            'placement' => 'top',
        ],
        [
            'titulo'   => 'Ingresar una bicicleta',
            'texto'    => 'Use el botón "Ingresar" para dar de alta una bicicleta nueva en el inventario de su sucursal, ya sea escaneando su número de serie o capturando los datos de forma manual.',
            'selector' => '[data-onboarding="vendedor-ingresar"]',
            'placement' => 'bottom',
        ],
    ],

    'root.dashboard' => [
        [
            'titulo'   => 'Panel Root',
            'texto'    => 'Desde aquí administra todos los negocios que operan en la plataforma, revisa los registros de auditoría del sistema y configura los parámetros globales que aplican a todos los negocios registrados.',
            'selector' => '[data-onboarding="sidebar-nav"]',
            'placement' => 'right',
        ],
        [
            'titulo'   => 'Estado de los negocios',
            'texto'    => 'Las tarjetas de la parte superior le muestran, de un vistazo, cuántos enlaces de registro hay disponibles y en qué estado se encuentra cada negocio: en periodo de prueba, activo, o con su prueba o suscripción expirada.',
            'selector' => '[data-onboarding="root-stats"]',
            'placement' => 'bottom',
        ],
        [
            'titulo'   => 'Crear un enlace de registro',
            'texto'    => 'Con el botón "Crear link" genera un enlace único para que un negocio nuevo se registre en la plataforma. El enlace expira automáticamente a las 24 horas, o en cuanto se utiliza una vez.',
            'selector' => '[data-onboarding="root-crear-link"]',
            'placement' => 'bottom',
        ],
        [
            'titulo'   => 'Enlaces generados',
            'texto'    => 'La tabla inferior muestra el historial completo de enlaces creados: cuántos vendedores permite cada uno, cuándo expiran, y si ya fueron utilizados, siguen disponibles o vencieron.',
            'selector' => '[data-onboarding="root-tabla-links"]',
            'placement' => 'top',
        ],
    ],

    /*
    |------------------------------------------------------------------
    | Administrador (rol 1)
    |------------------------------------------------------------------
    */

    'admin.movimientos.index' => [
        [
            'titulo' => 'Seguimiento de movimientos',
            'texto'  => 'Busque una bicicleta por su número de serie para consultar el historial completo de movimientos entre sucursales: cuándo se trasladó, desde qué sucursal, hacia cuál, y quién autorizó el traslado.',
        ],
    ],

    'admin.cajas.index' => [
        [
            'titulo'   => 'Control de cajas',
            'texto'    => 'Esta es la vista consolidada de todas sus sucursales: el total de dinero en cajas, las ventas del día, cuántas sesiones de caja están abiertas actualmente y cuántas sucursales todavía no tienen una caja asignada.',
            'selector' => '[data-onboarding="cajas-resumen"]',
            'placement' => 'bottom',
        ],
        [
            'titulo'   => 'Asignar una caja',
            'texto'    => 'Si una sucursal todavía no tiene caja, asígnesela con el botón "+ Asignar caja" (o desde su propia tarjeta). Sin una caja asignada, el vendedor de esa sucursal no podrá abrir sesiones de trabajo ni registrar cobros.',
            'selector' => '[data-onboarding="cajas-asignar-btn"]',
            'placement' => 'bottom',
        ],
        [
            'titulo'   => 'Tarjeta de sucursal',
            'texto'    => 'Cada tarjeta le muestra si la caja de esa sucursal está abierta o cerrada, el total acumulado en el sistema, las ventas registradas en el día y el fondo inicial con el que se abrió la sesión activa.',
            'selector' => '[data-onboarding="cajas-tarjetas"]',
            'placement' => 'top',
        ],
        [
            'titulo'   => 'Ingreso, retiro y cierre forzado',
            'texto'    => 'Con la caja abierta puede registrar un ingreso o retiro de dinero de forma manual, o forzar el cierre de la sesión si el vendedor no lo hizo (por ejemplo, si quedó abierta desde un día anterior).',
            'selector' => '[data-onboarding="cajas-ingreso"]',
            'placement' => 'top',
        ],
        [
            'titulo'   => 'Límites de gasto',
            'texto'    => 'Defina un tope semanal de gastos por sucursal. Se reinicia todos los lunes y le avisará si se supera, sin importar si la caja está abierta o cerrada. Esto le ayuda a controlar que ninguna sucursal gaste de más sin su autorización.',
            'selector' => '[data-onboarding="cajas-limites"]',
            'placement' => 'top',
        ],
        [
            'titulo'   => 'Ver el detalle completo',
            'texto'    => '"Ver detalle" lo llevará a la caja de esa sucursal, con el historial completo de movimientos, sesiones abiertas y cerradas, y los cortes realizados.',
            'selector' => '[data-onboarding="cajas-ver-detalle"]',
            'placement' => 'top',
        ],
    ],

    'admin.cajas.show' => [
        [
            'titulo' => 'Detalle de caja',
            'texto'  => 'Si esta sucursal todavía no tiene caja asignada, puede crearla desde aquí. Una vez creada, podrá registrar ingresos, retiros y ajustes manuales, consultar el historial completo de movimientos y definir un límite semanal de gastos para mantener el control financiero de la sucursal.',
        ],
    ],

    'admin.garantias.index' => [
        [
            'titulo' => 'Garantías',
            'texto'  => 'Desde esta sección configura la política de garantía de cada marca que maneja en su catálogo: el tiempo de cobertura, los componentes excluidos y la documentación asociada. También puede gestionar los reclamos de garantía que reporten sus sucursales.',
        ],
    ],

    'admin.garantias.reclamos' => [
        [
            'titulo'   => 'Reclamos de garantía',
            'texto'    => 'Consulte todos los reclamos que reportaron sus sucursales. Filtre por número de serie o por estado (en revisión, aprobado, rechazado, finalizado) para encontrar uno rápidamente entre todos los registrados.',
            'selector' => '[data-onboarding="reclamos-filtros"]',
            'placement' => 'bottom',
        ],
        [
            'titulo'   => 'Gestionar un reclamo',
            'texto'    => 'Seleccione "Gestionar" para ver el motivo del reclamo, el kilometraje de la bicicleta y, si está disponible, la sugerencia de la inteligencia artificial, que solo sugiere: la decisión final siempre es suya.',
            'selector' => '[data-onboarding="reclamos-gestionar-btn"]',
            'placement' => 'left',
        ],
        [
            'titulo' => 'Aprobar o rechazar',
            'texto'  => 'Mientras la orden de trabajo asociada esté en revisión, apruebe o rechace el reclamo y deje una nota con su justificación. Esta decisión queda registrada como parte del historial del reclamo.',
        ],
        [
            'titulo' => 'Procesar el reemplazo',
            'texto'  => 'Una vez aprobado el reclamo y resuelta la reparación, procese el reemplazo de garantía para generar la nueva cobertura del componente, de acuerdo con la política de reemplazo definida para esa marca.',
        ],
    ],

    'admin.garantias.marcas.editar' => [
        [
            'titulo'   => 'Garantía activa',
            'texto'    => 'El interruptor de la parte superior activa o desactiva la garantía de esta marca para todo su negocio. Se habilita únicamente después de que haya configurado la póliza al menos una vez.',
            'selector' => '[data-onboarding="marca-toggle-activa"]',
            'placement' => 'bottom',
        ],
        [
            'titulo'   => 'Póliza en PDF',
            'texto'    => 'Suba la póliza de la marca en formato PDF (máximo 4 MB). La inteligencia artificial la procesa automáticamente y extrae los componentes cubiertos, para que usted los revise y ajuste antes de guardarlos.',
            'selector' => '[data-onboarding="marca-pdf-section"]',
            'placement' => 'right',
        ],
        [
            'titulo'   => 'Política de reemplazo',
            'texto'    => 'Seleccione qué garantía recibe un componente sustituido: "Heredar" el tiempo restante de la garantía original, "Nueva completa" con la duración total desde cero, o "Mini" con una cantidad de días configurable, pensada para reemplazos puntuales en garantía.',
            'selector' => '[data-onboarding="marca-politica-section"]',
            'placement' => 'right',
        ],
        [
            'titulo'   => 'Componentes con garantía',
            'texto'    => 'Revise lo que la inteligencia artificial extrajo del PDF: el nombre de cada componente, sus meses de cobertura, qué incluye y sus excepciones. Puede editar cualquier dato, agregar un componente de forma manual, marcarlo como serializable o excluirlo si se trata de un consumible.',
            'selector' => '[data-onboarding="marca-componentes-section"]',
            'placement' => 'left',
        ],
        [
            'titulo'   => 'Sugerencias con IA',
            'texto'    => 'En cada componente puede generar las excepciones de garantía con inteligencia artificial, o reutilizar las excepciones que ya definió antes para otro componente similar, para ahorrar tiempo al configurar varias marcas.',
            'selector' => '[data-onboarding="marca-componentes-section"]',
            'placement' => 'left',
        ],
    ],

    'admin.cupones.index' => [
        [
            'titulo' => 'Cupones',
            'texto'  => 'Cree códigos de descuento para impulsar sus ventas: defina si el beneficio es un porcentaje, un monto fijo, un accesorio gratis o un beneficio especial de mantenimiento. Puede establecer reglas de uso, fechas de vigencia, y activar o desactivar cada cupón cuando lo necesite.',
        ],
    ],

    // Este tour no se dispara al cargar la página: se muestra la primera vez
    // que se abre el modal de crear/editar cupón (ver trigger-event en
    // administrador/cupones/index.blade.php).
    'admin.cupones.modal-crear' => [
        [
            'titulo'   => 'Tipo de cupón',
            'texto'    => 'Seleccione qué beneficio otorga el cupón: un descuento en el total de la venta o en un producto específico, un accesorio gratis, o un beneficio especial en el servicio de mantenimiento.',
            'selector' => '[data-onboarding="cupon-tipo"]',
            'placement' => 'bottom',
        ],
        [
            'titulo'   => 'Nombre del cupón',
            'texto'    => 'Asígnele un nombre para identificarlo internamente en sus reportes, y un código: el que el vendedor escribe o el cliente presenta al momento de la compra. Puede generar el código automáticamente con el botón "Generar".',
            'selector' => '[data-onboarding="cupon-nombre"]',
            'placement' => 'bottom',
        ],
        [
            'titulo'   => 'Configurar descuento',
            'texto'    => 'Según el tipo que haya seleccionado, defina aquí si el descuento es un porcentaje o un monto fijo, cuál accesorio se regala, o en qué consiste el beneficio de mantenimiento.',
            'selector' => '[data-onboarding="cupon-descuento"]',
            'placement' => 'bottom',
        ],
        [
            'titulo'   => 'Condiciones',
            'texto'    => 'La condición de sucursal es obligatoria (puede elegir "Todas" para que aplique en cualquiera). Agregue más condiciones si desea limitar el cupón a una marca, un modelo, un voltaje específico o a un monto mínimo de compra.',
            'selector' => '[data-onboarding="cupon-condiciones"]',
            'placement' => 'top',
        ],
    ],

    'admin.personal.index' => [
        [
            'titulo' => 'Su equipo',
            'texto'  => 'Administre a los vendedores asignados a sus sucursales: agregue uno nuevo capturando sus datos y credenciales de acceso, edite su información cuando sea necesario, o elimínelo si deja de trabajar en su negocio.',
        ],
    ],

    'admin.catalogo.index' => [
        [
            'titulo' => 'Catálogo',
            'texto'  => 'Arme el catálogo completo de bicicletas de su negocio: registre las marcas que maneja, los modelos disponibles de cada una, los colores en los que vienen y los voltajes de batería compatibles con cada modelo. Este catálogo es la base para registrar su inventario.',
        ],
    ],

    'admin.config.index' => [
        [
            'titulo' => 'Configuración general',
            'texto'  => 'Estos ajustes se aplican por igual a todas sus sucursales: cómo se entregan los comprobantes a sus clientes (ticket, factura o ambos), los porcentajes utilizados en sus cálculos, y otras reglas generales que rigen la operación de su negocio.',
        ],
    ],

    'admin.productos.index' => [
        [
            'titulo' => 'Precios',
            'texto'  => 'Defina los precios de venta de sus bicicletas y accesorios. Estos precios se aplican de forma centralizada, así que un cambio que haga aquí se refleja automáticamente en el punto de venta de todas sus sucursales.',
        ],
    ],

    'bicicletas.index' => [
        [
            'titulo' => 'Stock de bicicletas',
            'texto'  => 'Consulte el inventario completo de bicicletas de todas sus sucursales en un solo lugar: modelo, color, número de serie y la sucursal en la que se encuentra actualmente cada unidad.',
        ],
    ],

    'admin.catalogo.voltajes.index' => [
        [
            'titulo' => 'Voltajes',
            'texto'  => 'En esta sección podrá agregar y administrar los voltajes de batería disponibles en su catálogo, para después asignarlos a los modelos de bicicleta que los utilicen.',
        ],
    ],

    'admin.bicicletas.create' => [
        [
            'titulo' => 'Registrar bicicletas',
            'texto'  => 'Dé de alta bicicletas nuevas en su inventario. Puede registrarlas una por una capturando sus datos de forma manual, o hacer una carga masiva escaneando los números de serie de varias unidades a la vez, ideal cuando recibe un pedido grande de su proveedor.',
        ],
    ],

    'admin.pedidos.create' => [
        [
            'titulo' => 'Nuevo pedido',
            'texto'  => 'Arme un pedido para un cliente: agregue las bicicletas que va a incluir, complete los datos de contacto y entrega del cliente, y genere el pedido para darle seguimiento hasta su entrega final.',
        ],
    ],

    'pedidos.rapido.crear' => [
        [
            'titulo' => 'Emisión rápida',
            'texto'  => 'Escanee una bicicleta para generar un pedido de forma inmediata, sin necesidad de completar el formulario extenso. Es útil cuando necesita emitir un pedido en el momento, por ejemplo durante una venta en mostrador.',
        ],
    ],

    'pedidos.realizar' => [
        [
            'titulo' => 'Completar el pedido',
            'texto'  => 'Siga el progreso del pedido paso a paso: desde la preparación de las bicicletas hasta la entrega final al cliente. Cada etapa queda registrada para que pueda darle seguimiento en cualquier momento.',
        ],
    ],

    /*
    |------------------------------------------------------------------
    | Vendedor / Sucursal (rol 2)
    |------------------------------------------------------------------
    */

    'ventas.index' => [
        [
            'titulo' => 'Ventas',
            'texto'  => 'Consulte el historial completo de ventas de su sucursal. Desde aquí puede iniciar una venta nueva, y revisar el detalle, la póliza de garantía o el ticket de cualquier venta que haya realizado anteriormente.',
        ],
    ],

    'ventas.create' => [
        [
            'titulo' => 'Nueva venta',
            'texto'  => 'Escanee el código QR o escriba el número de serie de cada bicicleta que va a vender, agregue accesorios adicionales si el cliente los solicita, y seleccione el método de pago (efectivo, tarjeta, transferencia, o una combinación de ellos).',
        ],
    ],

    'ventas.show' => [
        [
            'titulo' => 'Detalle de venta',
            'texto'  => 'Consulte el resumen completo de esta venta: las bicicletas y accesorios vendidos, los métodos de pago utilizados, y los accesos directos para descargar la póliza de garantía y el ticket de compra.',
        ],
    ],

    'garantias.index' => [
        [
            'titulo' => 'Garantías',
            'texto'  => 'Busque una bicicleta por su número de serie para verificar si todavía cuenta con garantía vigente y qué componentes cubre. Si el cliente reporta una falla cubierta, genere un reclamo desde aquí para que el administrador lo revise.',
        ],
    ],

    'garantias.show' => [
        [
            'titulo' => 'Garantía de la bicicleta',
            'texto'  => 'Consulte el estado actual de la garantía de esta bicicleta y el historial completo de reclamos que se hayan generado sobre ella. Si el cliente reporta una falla nueva, puede generar un reclamo adicional desde aquí.',
        ],
    ],

    'sucursal.productos.index' => [
        [
            'titulo' => 'Precios',
            'texto'  => 'Consulte los precios de venta y el inventario disponible de bicicletas y accesorios que puede ofrecer a sus clientes en esta sucursal. Estos precios los define el administrador y se actualizan de forma automática.',
        ],
    ],

    'reparaciones.index' => [
        [
            'titulo'   => 'Órdenes de trabajo (OT)',
            'texto'    => 'La lista de la izquierda muestra todas sus órdenes de trabajo, con filtros por estado. Seleccione "Nueva OT" para recibir una bicicleta y abrir una orden nueva.',
            'selector' => '[data-onboarding="ot-lista"]',
            'placement' => 'right',
        ],
        [
            'titulo'   => 'Panel de detalle',
            'texto'    => 'Al seleccionar una orden de trabajo podrá ver los datos del cliente, la bicicleta, el problema reportado y una barra de progreso con las etapas: Recibida → Diagnóstico → Cotización → En proceso → Lista → Entregada.',
            'selector' => '[data-onboarding="ot-panel-detalle"]',
            'placement' => 'left',
        ],
        [
            'titulo' => 'Diagnóstico y cotización',
            'texto'  => 'Cargue el diagnóstico del técnico, el costo de mano de obra y las piezas necesarias (puede buscarlas en su catálogo o agregarlas de forma manual). Si agrega piezas, al guardar se enviará la cotización al cliente por correo electrónico de forma automática.',
        ],
        [
            'titulo' => 'Resolver manualmente',
            'texto'  => 'Si el cliente respondió en persona o la cotización expiró sin respuesta, use "Resolver manualmente" para registrar si acepta todo lo cotizado, solo una parte, o rechaza la reparación por completo.',
        ],
        [
            'titulo' => 'Cobrar y entregar',
            'texto'  => 'Cuando la orden de trabajo esté lista, podrá cobrar y entregar la bicicleta (o entregarla directamente si no tiene costo). También puede cancelar la orden en cualquier momento antes de la entrega final.',
        ],
    ],

    'reparaciones.create' => [
        [
            'titulo' => 'Nueva orden de trabajo',
            'texto'  => 'Reciba la bicicleta que trae el cliente y abra una orden de trabajo (OT) para documentar el problema reportado. A partir de aquí comienza todo el proceso de diagnóstico, cotización y reparación.',
        ],
    ],

    'reparaciones.cobrar.form' => [
        [
            'titulo' => 'Cobrar la reparación',
            'texto'  => 'Cuando el cliente venga a retirar su bicicleta ya reparada, cobre aquí el total de la orden de trabajo (mano de obra más piezas utilizadas) y seleccione el método de pago para completar la entrega.',
        ],
    ],

    'stock_piezas.index' => [
        [
            'titulo' => 'Piezas y stock',
            'texto'  => 'Consulte el inventario de piezas sueltas que utiliza en las reparaciones: repuestos, insumos y componentes, junto con el stock disponible de cada uno. Registre aquí las entradas de piezas nuevas que reciba de su proveedor.',
        ],
    ],

    'robo.index' => [
        [
            'titulo' => 'Reporte de robo',
            'texto'  => 'Reporte una bicicleta robada capturando su número de serie, para que quede marcada en el sistema y otras sucursales puedan identificarla. También puede verificar si una bicicleta que le ofrecen para compra o reparación tiene un reporte de robo activo.',
        ],
    ],

    'caja.index' => [
        [
            'titulo' => 'Mi caja',
            'texto'  => 'Abra su caja al iniciar su turno de trabajo, registre los ingresos y retiros de dinero que realice durante el día, y haga el corte correspondiente al finalizar para cuadrar el efectivo.',
        ],
    ],

    'ubicacion.index' => [
        [
            'titulo' => 'Ubicación de su sucursal',
            'texto'  => 'Marque en el mapa la ubicación exacta de su sucursal. Esta información se utiliza para mostrarla a los clientes que buscan sucursales cercanas, y para generar reportes basados en ubicación.',
        ],
    ],

    /*
    |------------------------------------------------------------------
    | Gestor (rol 5)
    |------------------------------------------------------------------
    */

    'gestor.dashboard' => [
        [
            'titulo' => 'Su panel de gestor',
            'texto'  => 'Aquí puede ver los negocios (administradores) con los que está vinculado como gestor externo. Desde este panel podrá dar seguimiento a los pedidos de esos negocios y administrar el catálogo de vehículos que comparten con usted.',
        ],
    ],

    'pedidos.index' => [
        [
            'titulo' => 'Pedidos',
            'texto'  => 'Consulte el listado completo de pedidos de bicicletas: revise el estado de cada uno, genere el documento en PDF para el cliente, y marque la entrega como completada una vez que el pedido se haya entregado.',
        ],
    ],

    'gestor.vehiculos.modelos.index' => [
        [
            'titulo' => 'Modelos',
            'texto'  => 'Consulte los modelos de bicicleta disponibles para los negocios con los que está vinculado. Cada modelo pertenece a una marca específica y sirve de base para armar el catálogo completo.',
        ],
    ],

    'gestor.vehiculos.colores.index' => [
        [
            'titulo' => 'Colores',
            'texto'  => 'Consulte y administre los colores disponibles para cada modelo de bicicleta en el catálogo. Estos colores quedarán disponibles para asignarlos al registrar bicicletas nuevas en el inventario.',
        ],
    ],

    'gestor.vehiculos.voltajes.index' => [
        [
            'titulo' => 'Voltajes',
            'texto'  => 'Consulte los voltajes de batería disponibles para asignar a los modelos de bicicleta eléctrica del catálogo.',
        ],
    ],

    'modelo-voltaje' => [
        [
            'titulo' => 'Definir voltaje',
            'texto'  => 'Asigne qué voltajes de batería están disponibles para cada modelo de bicicleta. Esta relación determina qué opciones de voltaje podrá elegir al registrar una bicicleta nueva en el inventario.',
        ],
    ],

];
