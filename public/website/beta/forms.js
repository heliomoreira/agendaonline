// Envio dos formulários com data-ajax sem recarregar a página.
// Sem JavaScript, os formulários fazem um POST normal e o Laravel redireciona de volta.
(function () {
	if (!window.fetch || !window.FormData) return;

	function mostrar(el, texto, tipo) {
		el.textContent = texto;
		el.className = "form-msg " + tipo;
	}

	document.querySelectorAll("form[data-ajax]").forEach(function (f) {
		var msg = f.querySelector(".form-msg"),
			btn = f.querySelector("button[type=submit]"),
			rotulo = btn.textContent;

		f.addEventListener("submit", function (e) {
			e.preventDefault();
			btn.disabled = true;
			btn.textContent = "A enviar…";
			mostrar(msg, "", "");
			f.querySelectorAll("[aria-invalid]").forEach(function (el) {
				el.removeAttribute("aria-invalid");
			});

			fetch(f.action, {
				method: "POST",
				body: new FormData(f),
				credentials: "same-origin",
				headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
			})
				.then(function (r) {
					return r
						.json()
						.catch(function () {
							return {};
						})
						.then(function (d) {
							return { ok: r.ok, status: r.status, d: d };
						});
				})
				.then(function (res) {
					if (res.ok) {
						f.reset();
						mostrar(msg, res.d.message || "Enviado.", "ok");
						return;
					}
					var texto = f.dataset.error;
					if (res.status === 422 && res.d.errors) {
						var campo = Object.keys(res.d.errors)[0];
						texto = res.d.errors[campo][0];
						var el = f.elements[campo];
						if (el && el.focus) {
							el.setAttribute("aria-invalid", "true");
							el.focus();
						}
					} else if (res.status === 419) {
						texto = "A página esteve aberta muito tempo. Recarregue-a e tente novamente.";
					} else if (res.status === 429) {
						texto = "Demasiadas tentativas seguidas. Aguarde um minuto e tente novamente.";
					} else if (res.status === 503 && res.d.message) {
						texto = res.d.message;
					}
					mostrar(msg, texto, "err");
				})
				.catch(function () {
					mostrar(msg, f.dataset.error, "err");
				})
				.then(function () {
					btn.disabled = false;
					btn.textContent = rotulo;
				});
		});
	});
})();
