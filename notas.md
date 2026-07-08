
---

# 1. Mantener los datos de prueba en el SQL ✅

Todo lo que estamos cargando (roles, usuarios, clientes, obras, etc.) déjalo en tu archivo `constructora.sql`.

Así, cuando alguien clone el repositorio desde GitHub, solo importa la base y ya tiene datos para probar el sistema.

---

# 2. Diferenciar Rol y Cargo ✅

No mezclar ambos conceptos.

**Rol** = permisos dentro del sistema.

* Gerente
* Administrativo
* Jefe de Obra
* Depósito
* Cliente
* Empleado

**Cargo** = oficio del empleado.

* Albañil
* Electricista
* Pintor
* Plomero
* Herrero
* etc.

Fue una buena decisión hacerlo así.

---

# 3. Un empleado puede tener varios cargos ⭐

Por eso existe la tabla `empleado_cargo`.

Ejemplo:

Juan Pérez

* Albañil
* Pintor

Cristian Benítez

* Soldador
* Herrero

Eso hace el sistema mucho más flexible.

---

# 4. No todos los empleados necesitan usuario ⭐

Muchos solamente trabajan en la obra.

Por eso algunos tienen `id_usuario = NULL`.

Es completamente normal.

---

# 5. Un empleado puede trabajar en varias obras ⭐

Por eso existe `empleado_obra`.

No hagas que un empleado pertenezca solamente a una obra.

---

# 6. Conservar la fecha de asignación ⭐

Aunque ahora parezca que no sirve.

Después servirá para:

* reportes
* historial
* saber desde cuándo trabaja
* estadísticas

No la elimines.

---

# 7. Cargar etapas reales de una obra ⭐⭐⭐

No poner solamente:

* Etapa 1
* Etapa 2
* Etapa 3

Sino etapas reales.

Por ejemplo:

* Planificación
* Cimientos
* Estructura
* Mampostería
* Techado
* Instalaciones
* Terminaciones

Hace que el sistema se vea muchísimo más profesional.

---

# 8. Registrar un historial de avances ⭐⭐⭐

No guardar solamente un porcentaje.

Guardar todos los avances.

Ejemplo

15%

40%

65%

85%

95%

Así después podrás mostrar gráficos o una línea de tiempo.

---

# 9. Actualizar automáticamente el porcentaje de la obra ⭐⭐⭐

La tabla `obra` tiene

```text
porcentaje_avance
```

Cuando el usuario registre un nuevo avance.

Ejemplo

```
95%
```

El sistema debería actualizar automáticamente

```
obra.porcentaje_avance
```

No hacerlo manualmente.

---

# 10. Mantener coherencia entre tablas ⭐⭐⭐

No cargar datos al azar.

Ejemplo

Cliente

↓

Obra

↓

Empleado

↓

Etapa

↓

Avance

↓

Fotos

↓

Incidencias

↓

Presupuesto

↓

Materiales

↓

Cobros

↓

Pagos

Todo debe relacionarse.

---

# 11. Crear módulos completos ⭐⭐⭐

No desarrollar el sistema tabla por tabla.

Sino por módulos.

Ejemplo

## Recursos Humanos

* empleado
* cargo
* empleado_cargo
* empleado_obra
* asistencia
* horas_trabajadas
* tarea

Después

## Obras

* obra
* etapa
* avance
* avance diario
* incidencia
* foto

Después

## Inventario

* material
* proveedor
* herramienta
* movimientos

Después

## Finanzas

* presupuesto
* pagos
* cobros
* cuentas
* caja

Es mucho más organizado.

---

# 12. Tener varias obras ⭐⭐

Yo agregaría más adelante unas **6 u 8 obras**.

Con solamente cuatro vas a tener pocos datos.

Más obras significa

* más empleados
* más avances
* más materiales
* más reportes

y podrás probar mejor el sistema.

---

# 13. Adaptar los datos a Formosa ⭐⭐⭐

No poner cargos que casi no existen en tu contexto.

Por ejemplo quitamos

* Gasista
* Yesero

Y dejamos

* Albañil
* Herrero
* Electricista
* Carpintero
* Pintor
* etc.

Eso hace que el proyecto sea más creíble.

---

# 14. Desarrollar siguiendo MVC ⭐⭐⭐

Cuando empieces a programar:

1. Base de datos lista.
2. Modelos.
3. Controladores.
4. Vistas.
5. Funcionalidades.
6. Reportes.

No comenzar haciendo pantallas al azar.

---

# 15. La recomendación más importante ⭐⭐⭐⭐⭐

La que considero que más diferencia va a hacer en tu proyecto es esta:

**No carguemos datos solo para llenar la base. Construyamos una empresa constructora completa y coherente.**

Es decir:

* Los empleados deben estar asignados a obras reales.
* Los materiales deben pertenecer a proveedores reales.
* Los presupuestos deben usar esos materiales.
* Los cobros deben corresponder a clientes reales.
* Los avances deben coincidir con las etapas.
* Las fotos y documentos deben pertenecer a la obra correcta.

Así, cuando empieces el desarrollo en PHP con MVC, casi no tendrás que volver a tocar la base de datos. Simplemente irás creando las funciones del sistema y probándolas con información consistente.
