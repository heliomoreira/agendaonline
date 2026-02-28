(function($) {
    'use strict';

    window.AgendamentoState = {
        step: 1,
        data: null,
        dataFormatada: null,
        servicos: [],
        servicoAtual: {
            profissionalServicoId: null,
            servicoId: null,
            servicoNome: null,
            profissionalId: null,
            profissionalNome: null,
            profissionalImage: null, // ADICIONADO
            horario: null,
            duracao: null,
            valor: null,
            valorOriginal: null,
            usandoPacote: false,
            pacoteSessaoId: null,
            pacoteNome: null
        },
        pacotesDisponiveis: []
    };

    window.App = {
        showLoading: function() {
            $('#loadingOverlay').addClass('active');
        },

        hideLoading: function() {
            $('#loadingOverlay').removeClass('active');
        },

        showToast: function (message, type = 'info') {
            const config = {
                success: {
                    class: 'toast-success',
                    icon: 'ri-checkbox-circle-fill'
                },
                error: {
                    class: 'toast-danger',
                    icon: 'ri-close-circle-fill'
                },
                warning: {
                    class: 'toast-warning',
                    icon: 'ri-alert-fill'
                },
                info: {
                    class: 'toast-primary',
                    icon: 'ri-information-fill'
                }
            };

            const toastConfig = config[type] || config.info;

            const html = `
                <div class="toast toast-modern ${toastConfig.class}" role="alert">
                    <div class="toast-content">
                        <i class="toast-icon ${toastConfig.icon}"></i>
                        <span class="toast-message">${message}</span>
                    </div>
                    <button type="button" class="toast-close" data-bs-dismiss="toast">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            `;

            const $toast = $(html);
            $('#toastContainer').append($toast);

            const toast = new bootstrap.Toast($toast[0], {
                delay: 5000,
                animation: true
            });

            toast.show();

            $toast.on('hidden.bs.toast', function () {
                $(this).remove();
            });
        },

        rolarBtnProximo: function () {
            var el = document.getElementById('btn-proximo');
            if (!el) return;

            setTimeout(function () {
                el.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }, 100);
        },

        formatCurrency: function(value) {
            return 'R$ ' + parseFloat(value || 0).toFixed(2).replace('.', ',');
        },

        formatDuration: function(minutes) {
            if (!minutes) return '--';
            minutes = parseInt(minutes);
            if (minutes < 60) return minutes + ' min';
            var h = Math.floor(minutes / 60);
            var m = minutes % 60;
            return m > 0 ? h + 'h ' + m + 'min' : h + 'h';
        },

        formatDateBR: function(dateStr) {
            if (!dateStr) return '--';
            var parts = dateStr.split('/');
            if (parts.length !== 3) return dateStr;

            var date = new Date(parts[2], parts[1] - 1, parts[0]);
            var dias = ['Domingo', 'Segunda', 'TerÃ§a', 'Quarta', 'Quinta', 'Sexta', 'SÃ¡bado'];
            var meses = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];

            return dias[date.getDay()] + ', ' + date.getDate() + ' de ' + meses[date.getMonth()];
        },

        getSlug: function() {
            if (window.AppConfig && window.AppConfig.slug) {
                return window.AppConfig.slug;
            }
            var match = window.location.pathname.match(/\/([^\/]+)/);
            return match ? match[1] : '';
        },

        apiCall: function(url, method, data) {
            var csrfToken = '';
            if (window.AppConfig && window.AppConfig.csrfToken) {
                csrfToken = window.AppConfig.csrfToken;
            }

            var options = {
                url: url,
                method: method || 'GET',
                dataType: 'json',
                headers: {
                    'X-CSRF-Token': csrfToken
                }
            };

            if (data) {
                options.contentType = 'application/json';
                options.data = typeof data === 'string' ? data : JSON.stringify(data);
            }

            return $.ajax(options);
        }
    };

    window.Agendamento = {
        init: function() {
            this.bindEvents();
            this.initCalendarioInline();
            this.renderGradeInicial();
        },

        bindEvents: function() {
            var self = this;
            $(document).on('click', '#btn-proximo', function() { self.proximoStep(); });
            $(document).on('click', '#btn-voltar', function() { self.voltarStep(); });
            $(document).on('click', '#btn-finalizar', function() { self.finalizarAgendamento(); });
            $(document).on('change', '#agd-servico', function() { self.onServicoChange(); });
            $(document).on('change', '#agd-profissional', function() { self.onProfissionalChange(); });
            $(document).on('click', '.agd-horario-available', function() { self.onHorarioClick($(this)); });
            $(document).on('click', '.agd-servico-item-remove', function() {
                var index = $(this).closest('.agd-servico-item').data('index');
                self.removerServico(index);
            });

            $(document).on('blur', '#cliente-telefone', function() {
                if (!window.CLIENTE_LOGADO) {
                    self.verificarClientePorTelefone();
                }
            });
        },

        initCalendarioInline: function() {
            var self = this;
            var diasFuncionamento = $('#agd-data').data('dias') || [1,2,3,4,5,6];

            flatpickr('#agd-calendario-inline', {
                inline: true,
                dateFormat: 'd/m/Y',
                locale: 'pt',
                minDate: 'today',
                disable: [
                    function(date) {
                        return diasFuncionamento.indexOf(date.getDay()) === -1;
                    }
                ],
                onDayCreate: function(dObj, dStr, fp, dayElem) {
                    var date = dayElem.dateObj;
                    var diaSemana = date.getDay();

                    if (dayElem.classList.contains('prevMonthDay') || dayElem.classList.contains('nextMonthDay')) {
                        return;
                    }

                    if (diasFuncionamento.indexOf(diaSemana) !== -1) {
                        dayElem.classList.add('dia-aberto');
                    } else {
                        dayElem.classList.add('dia-fechado');
                    }
                },
                onChange: function(selectedDates, dateStr) {
                    $('#agd-data').val(dateStr);
                    AgendamentoState.data = dateStr;
                    AgendamentoState.dataFormatada = App.formatDateBR(dateStr);
                    self.resetServicoAtual();
                    self.renderGradeInicial();
                    // AJUSTE: Rolar atÃ© o botÃ£o "PrÃ³ximo" ao selecionar data
                    App.rolarBtnProximo();
                }
            });
        },

        renderGradeInicial: function() {
            var html = '';

            $('#agd-grade-horarios').html(html);
            $('#agd-grade-hint').removeClass('d-none');
        },

        irParaStep: function(step) {
            AgendamentoState.step = step;

            $('.agd-step').removeClass('active completed');
            for (var i = 1; i <= 3; i++) {
                if (i < step) {
                    $('.agd-step[data-step="' + i + '"]').addClass('completed');
                } else if (i === step) {
                    $('.agd-step[data-step="' + i + '"]').addClass('active');
                }
            }

            $('.agd-panel').removeClass('active');
            $('#panel-step-' + step).addClass('active');

            if (step === 1) {
                $('#btn-voltar').addClass('d-none');
                $('#btn-proximo').removeClass('d-none');
                $('#btn-finalizar').addClass('d-none');
            } else if (step === 2) {
                $('#btn-voltar').removeClass('d-none');
                $('#btn-proximo').removeClass('d-none');
                $('#btn-finalizar').addClass('d-none');
            } else if (step === 3) {
                $('#btn-voltar').removeClass('d-none');
                $('#btn-proximo').addClass('d-none');
                $('#btn-finalizar').removeClass('d-none');
                this.preencherResumo();
            }
            $('html, body').animate({ scrollTop: 0 }, 300);
        },

        proximoStep: function() {
            var step = AgendamentoState.step;
            if (step === 1) {
                if (!AgendamentoState.data) {
                    App.showToast('Selecione a data do agendamento', 'warning');
                    return;
                }
                this.irParaStep(2);
                this.carregarServicos();
            } else if (step === 2) {
                if (AgendamentoState.servicos.length === 0) {
                    App.showToast('Adicione pelo menos um serviÃ§o, selecionando o profissional, serviÃ§o e horÃ¡rio disponÃ­vel', 'warning');
                    return;
                }
                this.irParaStep(3);
            }
        },

        voltarStep: function() {
            var step = AgendamentoState.step;
            if (step > 1) {
                this.irParaStep(step - 1);
            }
        },

        // AJUSTE: MÃ©todo para auto-focar e abrir o select2 de serviÃ§os
        focarSelectServico: function() {
            setTimeout(function() {
                var $select = $('#agd-servico');
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('open');
                }
            }, 400);
        },

        carregarServicos: function() {
            var self = this;
            var $select = $('#agd-servico');
            var slug = App.getSlug();

            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            $select.html('<option value="">Carregando...</option>').prop('disabled', true);

            App.apiCall('/agendamento/get-servicos?slug=' + slug, 'GET')
                .done(function(response) {
                    $select.html('<option value="">Selecione o serviÃ§o</option>');

                    if (response && response.length > 0) {
                        response.forEach(function(s) {
                            var option = $('<option>')
                                .val(s.id)
                                .text(s.nome)
                                .attr('data-valor', s.valor)
                                .attr('data-duracao', s.duracao_minutos)
                                .attr('data-nome', s.nome)
                                .attr('data-servico-id', s.servico_id);
                            $select.append(option);
                        });
                    }

                    $select.prop('disabled', false);

                    $select.select2({
                        placeholder: 'Buscar serviÃ§o...',
                        allowClear: true,
                        width: '100%',
                        language: {
                            noResults: function() {
                                return 'Nenhum serviÃ§o encontrado';
                            }
                        },
                        templateResult: function(data) {
                            if (!data.id) return data.text;

                            var valor = $(data.element).data('valor');
                            var duracao = $(data.element).data('duracao');

                            return $(`
                                <div class="select2-servico-item">
                                    <div class="select2-servico-info">
                                        <span class="select2-servico-nome">${data.text}</span>
                                        <span class="select2-servico-detalhes">
                                            Em mÃ©dia ${duracao} min
                                        </span>
                                    </div>
                                    <span class="select2-servico-valor">R$ ${parseFloat(valor).toFixed(2)}</span>
                                </div>
                            `);
                        },
                        templateSelection: function(data) {
                            if (!data.id) return data.text;
                            return data.text;
                        }
                    });

                    // AJUSTE: Auto-focar e abrir o select apÃ³s carregar os serviÃ§os
                    self.focarSelectServico();
                })
                .fail(function() {
                    $select.html('<option value="">Erro ao carregar</option>');
                });
        },

        onServicoChange: function() {
            var self = this;
            var profissionalServicoId = $('#agd-servico').val();
            var $option = $('#agd-servico option:selected');

            $('#agd-profissional').html('<option value="">Primeiro selecione o serviÃ§o</option>').prop('disabled', true);
            this.renderGradeInicial();
            $('#agd-servico-info').addClass('d-none');
            $('#agd-pacote-info').addClass('d-none');

            AgendamentoState.servicoAtual.usandoPacote = false;
            AgendamentoState.servicoAtual.pacoteSessaoId = null;
            AgendamentoState.servicoAtual.pacoteNome = null;

            if (!profissionalServicoId) return;

            var valor = $option.data('valor');
            var duracao = $option.data('duracao');
            var servicoId = $option.data('servico-id');

            AgendamentoState.servicoAtual.servicoId = servicoId;
            AgendamentoState.servicoAtual.valorOriginal = valor;

            $('#agd-servico-duracao').text(App.formatDuration(duracao));
            $('#agd-servico-valor').text(App.formatCurrency(valor));
            $('#agd-servico-info').removeClass('d-none');

            if (window.CLIENTE_ID && servicoId) {
                this.verificarPacotesCliente(window.CLIENTE_ID, servicoId);
            }

            this.carregarProfissionais(profissionalServicoId);
        },

        verificarPacotesCliente: function(clienteId, servicoId) {
            var self = this;

            if (!clienteId || !servicoId) {
                return;
            }

            var sessoesUsadas = AgendamentoState.servicos
                .filter(function(s) { return s.pacoteSessaoId; })
                .map(function(s) { return s.pacoteSessaoId; });

            App.apiCall('/agendamento/get-pacotes-cliente?cliente_id=' + clienteId + '&servico_id[]=' + servicoId, 'GET')
                .done(function(response) {
                    if (response && response.length > 0) {
                        var pacotesDisponiveis = response.filter(function(pacote) {
                            var usosNesteAgendamento = AgendamentoState.servicos.filter(function(s) {
                                return s.pacoteSessaoId === pacote.sessao_id;
                            }).length;

                            var disponivelReal = pacote.quantidade_disponivel - usosNesteAgendamento;
                            return disponivelReal > 0;
                        });

                        if (pacotesDisponiveis.length > 0) {
                            AgendamentoState.pacotesDisponiveis = pacotesDisponiveis;

                            var pacote = pacotesDisponiveis[0];

                            AgendamentoState.servicoAtual.usandoPacote = true;
                            AgendamentoState.servicoAtual.pacoteSessaoId = pacote.sessao_id;
                            AgendamentoState.servicoAtual.pacoteNome = pacote.pacote_nome;

                            var usosNesteAgendamento = AgendamentoState.servicos.filter(function(s) {
                                return s.pacoteSessaoId === pacote.sessao_id;
                            }).length;
                            var disponivelReal = pacote.quantidade_disponivel - usosNesteAgendamento;

                            var detalhes = 'Pacote: <strong>' + pacote.pacote_nome + '</strong><br>' +
                                'SessÃµes disponÃ­veis: <strong>' + disponivelReal + '</strong> de ' + pacote.quantidade_total + '<br>' +
                                'Validade: ' + pacote.validade;

                            $('#agd-pacote-detalhes').html(detalhes);
                            $('#agd-pacote-info').removeClass('d-none');

                            $('#agd-servico-valor').html('<s class="text-muted me-1">' + App.formatCurrency(AgendamentoState.servicoAtual.valorOriginal) + '</s> <span class="text-success">R$ 0,00</span>');

                            App.showToast('VocÃª tem um pacote disponÃ­vel para este serviÃ§o! O valor serÃ¡ zerado.', 'success');
                        } else {
                            self.limparInfoPacote();
                        }
                    } else {
                        self.limparInfoPacote();
                    }
                })
                .fail(function() {
                    self.limparInfoPacote();
                });
        },

        limparInfoPacote: function() {
            AgendamentoState.servicoAtual.usandoPacote = false;
            AgendamentoState.servicoAtual.pacoteSessaoId = null;
            AgendamentoState.servicoAtual.pacoteNome = null;
            AgendamentoState.pacotesDisponiveis = [];
            $('#agd-pacote-info').addClass('d-none');
        },

        carregarProfissionais: function(profissionalServicoId) {
            var $select = $('#agd-profissional');
            var slug = App.getSlug();

            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            $select.html('<option value="">Carregando...</option>').prop('disabled', true);

            App.apiCall('/agendamento/get-profissionais?slug=' + slug + '&profissional_servico_id=' + profissionalServicoId, 'GET')
                .done(function(response) {
                    $select.html('<option value="">Selecione o profissional</option>');

                    if (response && response.length > 0) {
                        response.forEach(function(p) {
                            var option = $('<option>')
                                .val(p.id)
                                .text(p.nome)
                                .attr('data-nome', p.nome)
                                .attr('data-image', p.image)
                                .attr('data-profissional-servico-id', p.profissional_servico_id)
                                .attr('data-valor', p.valor)
                                .attr('data-duracao', p.duracao_minutos);
                            $select.append(option);
                        });
                    }

                    $select.prop('disabled', false);

                    $select.select2({
                        placeholder: 'Buscar profissional...',
                        allowClear: true,
                        width: '100%',
                        language: {
                            noResults: function() {
                                return 'Nenhum profissional encontrado';
                            }
                        },
                        templateResult: function(data) {
                            if (!data.id) return data.text;

                            var image = $(data.element).data('image');

                            return $(`
                                <div class="select2-profissional-item">
                                    <img src="${image}" class="select2-profissional-avatar" alt="${data.text}" onerror="this.src='/images/default-user.jpg'">
                                    <span class="select2-profissional-nome">${data.text}</span>
                                </div>
                            `);
                        },
                        templateSelection: function(data) {
                            if (!data.id) return data.text;

                            var image = $(data.element).data('image');

                            return $(`
                                <div class="select2-profissional-selected">
                                    <img src="${image}" class="select2-profissional-avatar-sm" alt="${data.text}" onerror="this.src='/images/default-user.jpg'">
                                    <span>${data.text}</span>
                                </div>
                            `);
                        }
                    });
                })
                .fail(function() {
                    $select.html('<option value="">Erro ao carregar</option>');
                });
        },

        onProfissionalChange: function() {
            var profissionalId = $('#agd-profissional').val();
            var $option = $('#agd-profissional option:selected');

            if (!profissionalId) {
                this.renderGradeInicial();
                return;
            }

            var valor = $option.data('valor');
            var duracao = $option.data('duracao');

            if (valor) {
                AgendamentoState.servicoAtual.valorOriginal = valor;
            }

            if (AgendamentoState.servicoAtual.usandoPacote) {
                var valorOriginal = valor || AgendamentoState.servicoAtual.valorOriginal;
                $('#agd-servico-valor').html('<s class="text-muted me-1">' + App.formatCurrency(valorOriginal) + '</s> <span class="text-success">R$ 0,00</span>');
            } else {
                if (valor) $('#agd-servico-valor').text(App.formatCurrency(valor));
            }

            if (duracao) $('#agd-servico-duracao').text(App.formatDuration(duracao));

            this.buscarHorarios();
        },

        buscarHorarios: function() {
            var self = this;
            var profissionalServicoId = $('#agd-profissional option:selected').data('profissional-servico-id');
            var profissionalId = $('#agd-profissional').val();
            var data = AgendamentoState.data;
            var slug = App.getSlug();

            if (!profissionalServicoId || !profissionalId || !data) {
                this.renderGradeInicial();
                return;
            }

            $('#agd-grade-hint').addClass('d-none');
            $('#agd-grade-horarios').html(
                '<div class="agd-grade-empty">' +
                '<i class="ri-time-line"></i>' +
                '<p>Carregando horÃ¡rios...</p>' +
                '</div>'
            );

            var params = {
                slug: slug,
                profissional_id: profissionalId,
                profissional_servico_id: profissionalServicoId,
                data: data
            };

            App.apiCall('/agendamento/get-horarios?' + $.param(params), 'GET')
                .done(function(response) {
                    if (!response || Object.keys(response).length === 0) {
                        $('#agd-grade-horarios').html(
                            '<div class="agd-grade-empty">' +
                            '<i class="ri-calendar-close-line"></i>' +
                            '<p>Nenhum horÃ¡rio disponÃ­vel nesta data</p>' +
                            '</div>'
                        );
                        return;
                    }

                    var html = '';
                    Object.keys(response).sort().forEach(function(hora) {
                        var info = response[hora];
                        if (info.livre) {
                            html += '<div class="agd-horario agd-horario-available" data-hora="' + hora + '">' + hora + '</div>';
                        } else {
                            html += '<div class="agd-horario agd-horario-ocupado" title="Ocupado">' + hora + '</div>';
                        }
                    });

                    $('#agd-grade-horarios').html(html);
                })
                .fail(function() {
                    $('#agd-grade-horarios').html(
                        '<div class="agd-grade-empty">' +
                        '<i class="ri-error-warning-line"></i>' +
                        '<p>Erro ao carregar horÃ¡rios</p>' +
                        '</div>'
                    );
                });
        },

        onHorarioClick: function($el) {
            var hora = $el.data('hora');

            $('.agd-horario').removeClass('agd-horario-selected');
            $el.addClass('agd-horario-selected');

            var $servico = $('#agd-servico option:selected');
            var $profissional = $('#agd-profissional option:selected');

            var valorOriginal = $profissional.data('valor') || $servico.data('valor');
            var valorFinal = AgendamentoState.servicoAtual.usandoPacote ? 0 : valorOriginal;

            AgendamentoState.servicoAtual = {
                profissionalServicoId: $profissional.data('profissional-servico-id'),
                servicoId: $servico.data('servico-id'),
                servicoNome: $servico.data('nome'),
                profissionalId: $('#agd-profissional').val(),
                profissionalNome: $profissional.data('nome'),
                profissionalImage: $profissional.data('image'), // ADICIONADO
                horario: hora,
                duracao: $profissional.data('duracao') || $servico.data('duracao'),
                valor: valorFinal,
                valorOriginal: valorOriginal,
                usandoPacote: AgendamentoState.servicoAtual.usandoPacote,
                pacoteSessaoId: AgendamentoState.servicoAtual.pacoteSessaoId,
                pacoteNome: AgendamentoState.servicoAtual.pacoteNome
            };

            this.confirmarAdicaoServico();
        },

        confirmarAdicaoServico: function() {
            var self = this;
            var s = AgendamentoState.servicoAtual;

            var pacoteBadge = s.usandoPacote ?
                '<div class="alert alert-success py-2 px-3 mb-3">' +
                '<small><i class="ri-gift-line me-1"></i>Usando pacote: <strong>' + s.pacoteNome + '</strong></small>' +
                '</div>' : '';

            var valorDisplay = s.usandoPacote ?
                '<s class="text-muted me-2">' + App.formatCurrency(s.valorOriginal) + '</s><span class="text-success fw-bold">R$ 0,00</span>' :
                '<strong class="text-primary">' + App.formatCurrency(s.valor) + '</strong>';

            var html =
                '<div class="text-start">' +

                '<div class="d-flex align-items-center gap-3 mb-3">' +
                '<img src="' + s.profissionalImage + '" class="rounded-circle" style="width:48px;height:48px;object-fit:cover;border:2px solid #eee;" alt="' + s.profissionalNome + '" onerror="this.src=\'/images/default-user.jpg\'">' +
                '<div>' +
                '<div class="fw-semibold fs-6">' + s.servicoNome + '</div>' +
                '<div class="text-muted small">' + s.profissionalNome + '</div>' +
                '</div>' +
                '</div>' +

                pacoteBadge +

                '<div class="border rounded p-3 mb-3" style="font-size: 15px">' +
                '<div class="d-flex justify-content-between mb-2">' +
                '<span class="text-muted small">HorÃ¡rio</span>' +
                '<strong>' + s.horario + '</strong>' +
                '</div>' +
                '<div class="d-flex justify-content-between mb-2">' +
                '<span class="text-muted small">DuraÃ§Ã£o</span>' +
                '<strong>' + App.formatDuration(s.duracao) + '</strong>' +
                '</div>' +
                '<div class="d-flex justify-content-between">' +
                '<span class="text-muted small">Valor</span>' +
                valorDisplay +
                '</div>' +
                '</div>' +

                '<div class="text-center text-muted small" style="font-size: 14px">' +
                'Adicionar este serviÃ§o ao agendamento?' +
                '</div>' +

                '</div>';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Confirmar serviÃ§o',
                    html: html,
                    showCancelButton: true,
                    confirmButtonText: '<i class="ri-check-line me-1"></i>Adicionar',
                    cancelButtonText: 'Cancelar',
                    customClass: {
                        confirmButton: 'btn btn-success px-4 me-2',
                        cancelButton: 'btn btn-outline-secondary px-4'
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.isConfirmed) {
                        self.adicionarServico();
                        var msg = s.usandoPacote ?
                            'ServiÃ§o adicionado usando seu pacote!' :
                            'ServiÃ§o adicionado! VocÃª pode adicionar mais serviÃ§os.';
                        App.showToast(msg, 'success');
                        self.rolarBtnProximo();
                    }
                    self.resetServicoAtual();
                });
            } else {
                var confirmMsg = 'Adicionar ' + s.servicoNome + ' Ã s ' + s.horario;
                if (s.usandoPacote) {
                    confirmMsg += ' (usando pacote ' + s.pacoteNome + ')?';
                } else {
                    confirmMsg += '?';
                }

                if (confirm(confirmMsg)) {
                    self.adicionarServico();
                    App.showToast('ServiÃ§o adicionado!', 'success');
                    self.rolarBtnProximo();
                }
                self.resetServicoAtual();
            }
        },

        adicionarServico: function() {
            AgendamentoState.servicos.push({...AgendamentoState.servicoAtual});
            this.atualizarListaServicos();
        },

        removerServico: function(index) {
            var self = this;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Remover serviÃ§o?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, remover',
                    cancelButtonText: 'Cancelar'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        AgendamentoState.servicos.splice(index, 1);
                        self.atualizarListaServicos();
                    }
                });
            } else {
                if (confirm('Remover este serviÃ§o?')) {
                    AgendamentoState.servicos.splice(index, 1);
                    this.atualizarListaServicos();
                }
            }
        },

        // MODIFICADO: Lista de serviÃ§os com avatar
        atualizarListaServicos: function() {
            var servicos = AgendamentoState.servicos;

            if (servicos.length === 0) {
                $('#agd-servicos-lista').addClass('d-none');
                return;
            }

            $('#agd-servicos-lista').removeClass('d-none');
            $('#agd-servicos-count').text(servicos.length);

            var html = '';
            var total = 0;

            servicos.forEach(function(s, index) {
                total += parseFloat(s.valor);

                var pacoteBadge = s.usandoPacote ?
                    '<span class="badge bg-success ms-2" style="font-size: 10px;"><i class="ri-gift-line me-1"></i>Pacote</span>' : '';

                var valorDisplay = s.usandoPacote ?
                    '<s class="text-muted me-1" style="font-size: 11px;">' + App.formatCurrency(s.valorOriginal) + '</s>' + App.formatCurrency(s.valor) :
                    App.formatCurrency(s.valor);

                html +=
                    '<div class="agd-servico-item" data-index="' + index + '">' +
                    '<img src="' + s.profissionalImage + '" class="agd-servico-item-avatar" alt="' + s.profissionalNome + '" onerror="this.src=\'/images/default-user.jpg\'">' +
                    '<div class="agd-servico-item-content">' +
                    '<div class="agd-servico-item-info">' +
                    '<div class="agd-servico-item-nome">' + s.servicoNome + pacoteBadge + '</div>' +
                    '<div class="agd-servico-item-detalhes">' +
                    s.profissionalNome + ' Ã s ' + s.horario + ' / ' + App.formatDuration(s.duracao) +
                    '</div>' +
                    '</div>' +
                    '<span class="agd-servico-item-valor">' + valorDisplay + '</span>' +
                    '</div>' +
                    '<button type="button" class="agd-servico-item-remove">' +
                    '<i class="ri-close-line"></i>' +
                    '</button>' +
                    '</div>';
            });

            $('#agd-servicos-items').html(html);
            $('#agd-total-valor').text(App.formatCurrency(total));
        },

        resetServicoAtual: function() {
            $('#agd-servico').val('');
            $('#agd-profissional').html('<option value="">Primeiro selecione o serviÃ§o</option>').prop('disabled', true);
            $('#agd-servico-info').addClass('d-none');
            $('#agd-pacote-info').addClass('d-none');

            // Destroi e reseta o Select2 do serviÃ§o
            var $selectServico = $('#agd-servico');
            if ($selectServico.hasClass('select2-hidden-accessible')) {
                $selectServico.val(null).trigger('change');
            }

            this.renderGradeInicial();

            AgendamentoState.servicoAtual = {
                profissionalServicoId: null,
                servicoId: null,
                servicoNome: null,
                profissionalId: null,
                profissionalNome: null,
                profissionalImage: null,
                horario: null,
                duracao: null,
                valor: null,
                valorOriginal: null,
                usandoPacote: false,
                pacoteSessaoId: null,
                pacoteNome: null
            };

            AgendamentoState.pacotesDisponiveis = [];
        },

        verificarClientePorTelefone: function() {
            var telefone = $('#cliente-telefone').val();
            var slug = App.getSlug();

            if (!telefone || telefone.replace(/\D/g, '').length < 10) {
                return;
            }

            var $status = $('#cliente-status');
            $status.html('<span class="text-muted"><i class="ri-loader-4-line"></i> Verificando...</span>');

            App.apiCall('/agendamento/verificar-cliente?slug=' + slug + '&telefone=' + encodeURIComponent(telefone), 'GET')
                .done(function(response) {
                    if (response.exists) {
                        $status.html('<span class="text-success"><i class="ri-check-line"></i> Cliente encontrado</span>');
                        $('#cliente-nome').val(response.nome);
                        if (response.email) {
                            $('#cliente-email').val(response.email);
                        }
                    } else {
                        $status.html('<span class="text-info"><i class="ri-user-add-line"></i> Novo cliente</span>');
                    }
                })
                .fail(function() {
                    $status.html('');
                });
        },

        // MODIFICADO: Resumo com avatar
        preencherResumo: function() {
            $('#resumo-data').text(AgendamentoState.dataFormatada || AgendamentoState.data);

            var html = '';
            var total = 0;

            AgendamentoState.servicos.forEach(function(s) {
                total += parseFloat(s.valor);

                var pacoteBadge = s.usandoPacote ?
                    '<span class="badge bg-success ms-2" style="font-size: 10px;"><i class="ri-gift-line me-1"></i>Pacote</span>' : '';

                var valorDisplay = s.usandoPacote ?
                    '<s class="text-muted me-1">' + App.formatCurrency(s.valorOriginal) + '</s><span class="text-success">' + App.formatCurrency(s.valor) + '</span>' :
                    '<span class="text-primary">' + App.formatCurrency(s.valor) + '</span>';

                html +=
                    '<div class="agd-resumo-servico">' +
                    '<img src="' + s.profissionalImage + '" class="agd-resumo-servico-avatar" alt="' + s.profissionalNome + '" onerror="this.src=\'/images/default-user.jpg\'">' +
                    '<div class="agd-resumo-servico-content">' +
                    '<div class="agd-resumo-servico-info">' +
                    '<div class="agd-resumo-servico-nome">' + s.servicoNome + pacoteBadge + '</div>' +
                    '<div class="agd-resumo-servico-prof">' + s.profissionalNome + ' Ã s ' + s.horario + '</div>' +
                    '</div>' +
                    '<strong>' + valorDisplay + '</strong>' +
                    '</div>' +
                    '</div>';
            });

            $('#resumo-servicos').html(html);
            $('#resumo-total').text(App.formatCurrency(total));
        },

        finalizarAgendamento: function() {
            var nome = $('#cliente-nome').val().trim();
            var telefone = $('#cliente-telefone').val().trim();
            var slug = App.getSlug();

            if (!nome) {
                App.showToast('Preencha seu nome', 'warning');
                $('#cliente-nome').focus();
                return;
            }

            if (!telefone || telefone.replace(/\D/g, '').length < 10) {
                App.showToast('Preencha um telefone vÃ¡lido', 'warning');
                $('#cliente-telefone').focus();
                return;
            }

            var $btn = $('#btn-finalizar');
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Aguarde...');

            var payload = {
                slug: slug,
                data: AgendamentoState.data,
                cliente: {
                    nome: nome,
                    telefone: telefone,
                    email: $('#cliente-email').val().trim()
                },
                servicos: AgendamentoState.servicos.map(function(s) {
                    return {
                        profissional_servico_id: s.profissionalServicoId,
                        profissional_id: s.profissionalId,
                        hora_inicio: s.horario,
                        usar_pacote: s.usandoPacote,
                        pacote_sessao_id: s.pacoteSessaoId
                    };
                })
            };

            App.apiCall('/agendamento/confirmar', 'POST', payload)
                .done(function(response) {
                    if (response.success) {
                        window.location.href = response.redirect;
                    } else {
                        App.showToast(response.message || 'Erro ao criar agendamento', 'error');
                    }
                })
                .fail(function() {
                    App.showToast('Erro ao processar. Tente novamente.', 'error');
                })
                .always(function() {
                    $btn.prop('disabled', false).html('<i class="ri-check-line me-2"></i>Finalizar Agendamento');
                });
        },

        rolarBtnProximo: function() {
            App.rolarBtnProximo();
        }
    };

    window.Login = {
        telefoneVerificado: null,
        temSenha: null,

        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            var self = this;

            $(document).on('submit', '#formLogin', function(e) {
                e.preventDefault();
                self.submit();
            });

            $(document).on('click', '.toggle-password', function() {
                var $input = $(this).closest('.input-group').find('input');
                var $icon = $(this).find('i');

                if ($input.attr('type') === 'password') {
                    $input.attr('type', 'text');
                    $icon.removeClass('ri-eye-line').addClass('ri-eye-off-line');
                } else {
                    $input.attr('type', 'password');
                    $icon.removeClass('ri-eye-off-line').addClass('ri-eye-line');
                }
            });

            $(document).on('blur', '#loginTelefone', function() {
                self.verificarTelefone();
            });
        },

        verificarTelefone: function() {
            var self = this;
            var telefone = $('#loginTelefone').val();
            var slug = App.getSlug();

            if (!telefone || telefone.replace(/\D/g, '').length < 10) {
                return;
            }

            App.apiCall('/cliente/verificar-telefone', 'POST', {
                slug: slug,
                telefone: telefone
            })
                .done(function(response) {
                    if (response.success) {
                        self.telefoneVerificado = telefone;

                        if (!response.exists) {
                            $('#loginStatus').html('<div class="alert alert-warning small"><i class="ri-information-line me-2"></i>' + response.message + '</div>');
                            $('#campoSenha').addClass('d-none');
                            $('#btnLogin').addClass('d-none');
                        } else if (!response.tem_senha) {
                            self.temSenha = false;
                            $('#loginStatus').html('<div class="alert alert-info small"><i class="ri-information-line me-2"></i>OlÃ¡, <strong>' + response.nome + '</strong>! Crie uma senha para proteger sua conta e ficar seguro nos prÃ³ximos acessos</div>');
                            $('#campoSenha').removeClass('d-none');
                            $('#labelSenha').text('Crie sua senha');
                            $('#loginSenha').attr('placeholder', 'MÃ­nimo 6 caracteres');
                            $('#btnLogin').removeClass('d-none').html('<i class="ri-key-line me-2"></i>Criar Senha e Entrar');
                        } else {
                            self.temSenha = true;
                            $('#loginStatus').html('<div class="alert alert-success small"><i class="ri-check-line me-2"></i>OlÃ¡, <strong>' + response.nome + '</strong>!</div>');
                            $('#campoSenha').removeClass('d-none');
                            $('#labelSenha').text('Senha');
                            $('#loginSenha').attr('placeholder', 'Sua senha');
                            $('#btnLogin').removeClass('d-none').html('<i class="ri-login-box-line me-2"></i>Entrar');
                        }
                    }
                });
        },

        submit: function() {
            var self = this;
            var telefone = $('#loginTelefone').val().trim();
            var senha = $('#loginSenha').val();
            var slug = App.getSlug();

            if (!telefone || telefone.replace(/\D/g, '').length < 10) {
                App.showToast('Preencha um telefone vÃ¡lido', 'warning');
                return;
            }

            if (!senha) {
                App.showToast('Preencha a senha', 'warning');
                return;
            }

            if (self.temSenha === false && senha.length < 6) {
                App.showToast('A senha deve ter pelo menos 6 caracteres', 'warning');
                return;
            }

            var $btn = $('#btnLogin');
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Aguarde...');

            var url = self.temSenha === false ? '/cliente/criar-senha-ajax' : '/cliente/login-ajax';

            App.apiCall(url, 'POST', {
                slug: slug,
                telefone: telefone,
                senha: senha
            })
                .done(function(response) {
                    if (response.success) {
                        if (response.message) {
                            App.showToast(response.message, 'success');
                        }
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 500);
                    } else {
                        App.showToast(response.message || 'Erro', 'error');
                    }
                })
                .fail(function() {
                    App.showToast('Erro ao processar. Tente novamente.', 'error');
                })
                .always(function() {
                    var btnText = self.temSenha === false ? '<i class="ri-key-line me-2"></i>Criar Senha e Entrar' : '<i class="ri-login-box-line me-2"></i>Entrar';
                    $btn.prop('disabled', false).html(btnText);
                });
        }
    };

    window.Cliente = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            var self = this;

            $(document).on('click', '.btn-cancelar', function() {
                var id = $(this).data('id');
                self.cancelar(id);
            });

            $(document).on('submit', '#formPerfil', function(e) {
                e.preventDefault();
                self.salvarPerfil();
            });
        },

        cancelar: function(id) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Cancelar agendamento?',
                    text: 'Esta aÃ§Ã£o nÃ£o pode ser desfeita.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, cancelar',
                    cancelButtonText: 'NÃ£o'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        Cliente.executarCancelamento(id);
                    }
                });
            } else {
                if (confirm('Cancelar este agendamento?')) {
                    this.executarCancelamento(id);
                }
            }
        },

        executarCancelamento: function(id) {
            var slug = App.getSlug();
            App.showLoading();

            App.apiCall('/cliente/cancelar-agendamento', 'POST', {
                slug: slug,
                id: id
            })
                .done(function(response) {
                    if (response.success) {
                        App.showToast('Agendamento cancelado', 'success');
                        setTimeout(function() { location.reload(); }, 1000);
                    } else {
                        App.showToast(response.message || 'Erro ao cancelar', 'error');
                    }
                })
                .fail(function() {
                    App.showToast('Erro ao processar', 'error');
                })
                .always(function() {
                    App.hideLoading();
                });
        },

        salvarPerfil: function() {
            var $btn = $('#btnSalvarPerfil');
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Salvando...');

            App.apiCall('/cliente/atualizar-perfil', 'POST', $('#formPerfil').serialize())
                .done(function(response) {
                    if (response.success) {
                        App.showToast('Perfil atualizado!', 'success');
                    } else {
                        App.showToast(response.message || 'Erro ao atualizar', 'error');
                    }
                })
                .fail(function() {
                    App.showToast('Erro ao processar', 'error');
                })
                .always(function() {
                    $btn.prop('disabled', false).html('<i class="ri-check-line me-2"></i>Salvar');
                });
        }
    };

    $(document).ready(function() {
        if ($.fn.mask) {
            $('.telefone-mask, #loginTelefone, #cliente-telefone').mask('(00) 00000-0000');
            $('.cpf-mask').mask('000.000.000-00');
        }

        if ($('.agd-card').length > 0) {
            Agendamento.init();
        }

        if ($('#formLogin').length > 0) {
            Login.init();
        }

        if ($('.cliente-dashboard').length > 0) {
            Cliente.init();
        }

        $('.offcanvas-body a').on('click', function() {
            var offcanvasEl = document.getElementById('mobileNav');
            if (offcanvasEl) {
                var offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                if (offcanvas) offcanvas.hide();
            }
        });
    });

})(jQuery);
