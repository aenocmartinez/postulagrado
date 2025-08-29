(function (global) {
  // Fuente: MEN / SNIES (dataset público en Datos Abiertos: n5yy-8nav)
  // Cargamos TODAS las IES reconocidas y exponemos window.CO_IES = [ 'NOMBRE IES', ... ]
  // También disparamos el evento "ies-oficial:ready"

  const DATASET_URL = "https://www.datos.gov.co/resource/n5yy-8nav.json";
  const CACHE_KEY = "co_ies_cache_v1";
  const CACHE_MS = 30 * 24 * 60 * 60 * 1000; // 30 días

  function now(){ return Date.now(); }

  // Normalizador para deduplicar y ordenar
  const norm = s => (s||"").normalize("NFC").trim();
  const strip = s => norm(s).toLowerCase().normalize("NFD").replace(/\p{Diacritic}/gu,"");

  // Title Case vistoso (sin gritar)
  const LOWER = new Set(['de','del','la','las','los','y','e','o','u','al']);
  function toTitle(s){
    return (s||"").toLowerCase().split(/\s+/).map((w,i)=>{
      if (i>0 && LOWER.has(w)) return w;
      return w.split('-').map(p => p.charAt(0).toUpperCase()+p.slice(1)).join('-');
    }).join(' ');
  }

  function detectNameKey(row){
    const keys = Object.keys(row);
    // intenta por patrones comunes
    let k = keys.find(k=>/nombre.*institu|institu.*nombre|razon.*social|^instituci.o?n$/i.test(k));
    if (k) return k;
    // fallback: busca la columna que más parezca "nombre" por longitud media
    let best = null, bestScore = -1;
    for (const key of keys){
      const v = String(row[key] ?? "");
      const score = (/_?/g.test(key) ? 1 : 0) + (v.length>10?1:0);
      if (score > bestScore){ best = key; bestScore = score; }
    }
    return best || keys[0];
  }

  async function fetchIES(){
    const url = new URL(DATASET_URL);
    url.searchParams.set("$limit","50000"); // margen amplio

    const res = await fetch(url.toString(), { headers: { "Accept": "application/json" } });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const rows = await res.json();
    if (!Array.isArray(rows) || rows.length === 0) throw new Error("Dataset vacío");

    const nameKey = detectNameKey(rows[0]);
    const set = new Map(); // nombre normalizado -> {value, label}

    for (const r of rows) {
      const raw = norm(r[nameKey]);
      if (!raw) continue;
      // Ej.: muchos vienen en MAYÚSCULA: usamos el valor oficial para el value y Title Case para mostrar
      const value = raw;            // oficial
      const label = toTitle(raw);   // visible
      const key = strip(raw);
      if (!set.has(key)) set.set(key, { value, label });
    }

    // Array ordenado por etiqueta visible (es)
    const collate = new Intl.Collator('es');
    const list = Array.from(set.values()).sort((a,b)=>collate.compare(a.label,b.label));

    return list;
  }

  async function main(){
    try{
      // cache
      const cached = JSON.parse(localStorage.getItem(CACHE_KEY) || "null");
      if (cached && (now() - cached.time) < CACHE_MS) {
        global.CO_IES = cached.data;
        global.dispatchEvent(new CustomEvent("ies-oficial:ready"));
        return;
      }

      const ies = await fetchIES();
      global.CO_IES = ies; // [{value, label}]
      localStorage.setItem(CACHE_KEY, JSON.stringify({ time: now(), data: ies }));
      global.dispatchEvent(new CustomEvent("ies-oficial:ready"));
    }catch(e){
      console.error("IES oficial (MEN/SNIES) error:", e);
      global.CO_IES = [];
      global.dispatchEvent(new CustomEvent("ies-oficial:ready"));
    }
  }

  main();
})(window);