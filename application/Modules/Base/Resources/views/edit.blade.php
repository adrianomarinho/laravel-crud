<!DOCTYPE html>
<html lang="en">
<head>
    <title>Página de Edição de Registro</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link href="{{asset('template/sweetalert/css/sweetalert.css')}}" rel="stylesheet">

    <style type="text/css">

        body {
            background-color: #ece9e9;
        }

    </style>
</head>
<body>

<div class="container pai" style="margin-top: 5%;">

    @include('includes.alerts')

    <div class="row">
        <div class="col-md-12">
            <a href="{{ route('index') }}" class="btn btn-link"><< Voltar para o cadastro</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h1 class="text-center text-success"> Página de Edição Registro</h1>
        </div>
    </div>

    <form action="{{ route('update') }}" method="post">
        <input type="hidden" name="id" value="{{encrypt($usuario->id)}}">
        @csrf
        <div class="row">
            <div class="form-group">
                <div class="col-md-2">
                    <label for="codigo">Código</label>
                    <input name="codigo" value="{{ $usuario->codigo ?? null }}" type="text" class="form-control" id="codigo">
                </div>
                <div class="col-md-8">
                    <label for="nome">Nome</label>
                    <input name="nome" value="{{ $usuario->nome ?? null }}" type="text" class="form-control" id="nome">
                </div>
                <div class="col-md-2">
                    <label for="documento">CPF/CNPJ</label>
                    <input name="documento" value="{{ $usuario->documento ?? null }}" type="text" class="form-control cpfcnpj" id="documento">
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="form-group">
                <div class="col-md-2">
                    <label for="cep">CEP</label>
                    <input name="cep" value="{{ $usuario->cep ?? null }}" type="text" class="form-control cep buscaCep" id="cep">
                </div>
                <div class="col-md-9">
                    <label for="logradouro">Logradouro</label>
                    <input name="logradouro" value="{{ $usuario->logradouro ?? null }}" type="text" class="form-control" id="logradouro">
                </div>
                <div class="col-md-1">
                    <label for="numero">Número</label>
                    <input name="numero" value="{{ $usuario->numero ?? null }}" type="text" class="form-control" id="numero">
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="form-group">
                <div class="col-md-4">
                    <label for="complemento">Complemento</label>
                    <input name="complemento" value="{{ $usuario->complemento ?? null }}" type="text" class="form-control" id="complemento">
                </div>
                <div class="col-md-2">
                    <label for="bairro">Bairro</label>
                    <input name="bairro" value="{{ $usuario->bairro ?? null }}" type="text" class="form-control" id="bairro">
                </div>
                <div class="col-md-5">
                    <label for="cidade">Cidade</label>
                    <input name="cidade" value="{{ $usuario->cidade ?? null }}" type="text" class="form-control" id="cidade">
                </div>
                <div class="col-md-1">
                    <label for="uf">UF</label>
                    <input name="uf" value="{{ $usuario->uf ?? null }}" type="text" class="form-control" id="uf">
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="form-group">
                <div class="col-md-4">
                    <label for="telefone">Telefone</label>
                    <input name="fone" value="{{ $usuario->fone ?? null }}" type="text" class="form-control telefone" id="telefone">
                </div>
                <div class="col-md-4">
                    <label for="limite">Limide de Crédito</label>
                    <input name="limite" value="{{ number_format($usuario->limiteCredito, 2, ',', '.') }}" type="text" class="form-control dinheiro" id="limite">
                </div>
                <div class="col-md-4">
                    <label for="validade">Validade</label>
                    <input name="validade" value="{{ $usuario->validade->format('Y-m-d') ?? null }}" type="date" class="form-control" id="validade">
                </div>
            </div>
        </div>
        <br>
        <button type="submit" class="btn btn-default btn-lg pull-right">Atualizar</button>

    </form>
</div>
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script src="{{ asset('js/jquery.inputmask.bundle.min.js') }}"></script>
<script src="{{asset('template/mask/mask_money.js')}}"></script>
<script src="{{asset('template/sweetalert/js/sweetalert.min.js')}}"></script>
<script>
    $('.cpfcnpj').inputmask({
        mask: ['999.999.999-99', '99.999.999/9999-99'],
        inputMode: 'numeric',
        keepStatic: true
    });

    $('.telefone').inputmask({
        mask: ['(99)9999-9999', '(99)99999-9999'],
        inputMode: 'numeric',
        keepStatic: true
    });

    $('.cep').inputmask({
        mask: ['99999-999'],
        inputMode: 'numeric',
        keepStatic: true
    });

    $('.data').inputmask({
        mask: ['99/99/9999'],
        inputMode: 'text',
        keepStatic: true
    });

    {{-- Busca CEP --}}
    $('.buscaCep').on('change', function (e) {
        e.preventDefault();

        //Modal Carregando
        swal({
            title: 'Aguarde...',
            text: '',
            allowOutsideClick: false,
            allowEscapeKey: false,
            onOpen: function () {
                swal.showLoading();
            }
        });

        // if (verificaInternet()) {

            var cep = $(this).val().replace(/\D/g, '');

            if (cep != "") {

                var validacep = /^[0-9]{8}$/;

                if (validacep.test(cep)) {

                    var divPai = $(this).closest('div.pai');

                    $(divPai).find("input[type=text][name='logradouro']").val("");
                    $(divPai).find("input[type=text][name='bairro']").val("");
                    $(divPai).find("input[type=text][name='complemento']").val("");
                    $(divPai).find("input[type=text][name='numero']").val("");
                    $(divPai).find("input[type=text][name='cidade']").val("");
                    $(divPai).find("input[type=text][name='uf']").val("");

                    $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {

                        if (!("erro" in dados)) {

                            console.log(dados);

                            $(divPai).find("input[type=text][name='logradouro']").val(dados.logradouro);
                            $(divPai).find("input[type=text][name='bairro']").val(dados.bairro);
                            $(divPai).find("input[type=text][name='complemento']").val(dados.complemento);
                            $(divPai).find("input[type=text][name='cidade']").val(dados.localidade);
                            $(divPai).find("input[type=text][name='uf']").val(dados.uf);

                            divPai.find("input[type=text]").removeAttr('disabled');

                            if ($("#select-estados").length) {
                                $(divPai).find("#select-estados [value=" + dados.uf + "]").attr('selected', 'true');
                                $(divPai).find("#select-estados [value=" + dados.uf + "]").select2().trigger('change');

                                divPai.find("#select-estados").removeAttr('disabled');

                                buscaCidadesPorEstado('#select-cidades', dados.uf, dados.localidade);

                            } else {
                                swal.close();
                            }

                        } else {

                            limpa_formulário_cep();
                            divPai.find("input[type=text]").removeAttr('disabled');

                            swal({
                                title: 'Cep não encontrado.',
                                type: 'warning'
                            });
                        }
                    });

                } else {

                    limpa_formulário_cep();

                    swal({
                        title: 'Cep não encontrado.',
                        type: 'warning'
                    });
                }

            } else {

                limpa_formulário_cep();
                swal({
                    title: 'O campo CEP não pode ser vazio. Tente novamente!',
                    type: 'warning'
                });
            }

        // } else {
        //
        //     swal({
        //         title: 'Atenção!',
        //         text: 'Você parece estar sem conexão com a internet e a busca pelo CEP não pode ser realizada.',
        //         type: 'warning',
        //     });
        //     return false;
        // }

    });

    {{-- Limpa formulário de endereço ao buscar novo CEP --}}
    function limpa_formulário_cep(div) {

        $("input[type=text][name='logradouro']").val("");
        $("input[type=text][name='bairro']").val("");
        $("input[type=text][name='complemento']").val("");
        $("input[type=text][name='cidade']").val("");
        $("input[type=text][name='uf']").val("");
    }

    function formatMoney($value)
    {
        return number_format($value, 2, ',', '.');
    }

</script>
</html>
