// colombia-geo.js (fragmento)
(function (global) {
  let CO_DEPARTAMENTOS = {};
  global.CO_DEPARTAMENTOS = CO_DEPARTAMENTOS;

  const ENDPOINT =
    "https://www.datos.gov.co/resource/vafm-j2df.json"
    + "?$select=nom_dpto,nom_mpio,tipo"
    + "&$where=upper(tipo)%20like%20%27MUNICIPIO%25%27%20OR%20upper(tipo)%20like%20%27DISTRITO%25%27%20OR%20upper(nom_mpio)%20like%20%27BOGOT%25%27"
    + "&$order=nom_dpto,nom_mpio"
    + "&$limit=50000";

  const normalizarDepto = (n) =>
    /bogotá/i.test(n || "") ? "Bogotá D.C." : (n || "").normalize("NFC").trim();

  fetch(ENDPOINT, { headers: { Accept: "application/json" } })
    .then(async (r) => {
      if (!r.ok) throw new Error(`HTTP ${r.status} - ${await r.text()}`);
      return r.json();
    })
    .then((rows) => {
      if (!Array.isArray(rows)) throw new Error("Respuesta inesperada");
      const tmp = {};
      for (const row of rows) {
        const depto = normalizarDepto(row.nom_dpto);
        const mpio  = (row.nom_mpio || "").normalize("NFC").trim();
        if (!depto || !mpio) continue;
        (tmp[depto] ||= []).push(mpio);
      }
      for (const k of Object.keys(tmp)) {
        tmp[k] = Array.from(new Set(tmp[k])).sort((a,b)=>a.localeCompare(b,"es"));
      }
      CO_DEPARTAMENTOS = Object.fromEntries(
        Object.entries(tmp).sort((a,b)=>a[0].localeCompare(b[0],"es"))
      );
      global.CO_DEPARTAMENTOS = CO_DEPARTAMENTOS;
      global.dispatchEvent(new CustomEvent("co-departamentos:ready"));
    })
    .catch(console.error);
})(window);
