<!DOCTYPE html>
<html lang="en">
<head>
    <title>Página de Registro</title>
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
            <h1 class="text-center text-success"> Lista de Registros</h1>
        </div>
    </div>
    <br>

    <div class="row">

        @isset($mensagem)
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info alert-dismissible " role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">×</span>
                        </button>
                        <strong>{!! $mensagem !!}</strong>
                    </div>
                </div>
            </div>
        @endisset
        <div class="col-md-12">

            <form action="{{ route('list') }}" method="get">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label for="cep">Busque por:</label>
                            <select name="campo" class="form-control">
                                <option value="" selected>Selecione...</option>
                                <option value="cep">Cep</option>
                                <option value="cidade">Cidade</option>
                                <option value="codigo">Código</option>
                                <option value="nome">Nome</option>
                            </select>
                        </div>
                        <div class="col-md-9">
                            <label for="valor">Com valor</label>
                            <input name="valor" type="text" class="form-control" id="valor" placeholder="Digite o termo a ser pesquisado...">
                        </div>
                        <div class="col-md-1">
                            <br>
                            <button type="submit" class="btn btn-default btn-sm pull-right">Filtrar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <hr>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
            <th>Código</th>
            <th>Nome</th>
            <th>Documento</th>
            <th>Cidade / UF</th>
            <th>Cep</th>
            <th>Ações</th>
            </thead>
            <tbody>
            @foreach($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->codigo }}</td>
                    <td>{{ $usuario->nome }}</td>
                    <td>{{ $usuario->documento }}</td>
                    <td>{{ $usuario->cidade }} / {{ $usuario->uf }}</td>
                    <td>{{ $usuario->cep }}</td>
                    <td>
                        <a href="{{route('edit', encrypt($usuario->id))}}" class="btn btn-xs btn-primary">
                            Editar
                        </a>
                        <a href="{{route('destroy', encrypt($usuario->id))}}" class="btn btn-xs btn-danger">
                            Excluir
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
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

    $('.money').inputmask({
        mask: ['00,00'],
        inputMode: 'float',
        keepStatic: true
    });

    $(function () {
        $(".dinheiro").maskMoney({
            prefix: 'R$ ',
            thousands: '.',
            decimal: ','
        }).maskMoney('mask', 0.00);
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

    {{--    --}}{{-- Verificar se há conexão com a internet --}}
    {{--    function verificaInternet() {--}}
    {{--        var online = navigator.onLine;--}}
    {{--        if (!online) {--}}
    {{--            console.log('Sem internet');--}}
    {{--        }--}}
    {{--        return online;--}}
    {{--    }--}}

    {{-- Limpa formulário de endereço ao buscar novo CEP --}}
    function limpa_formulário_cep(div) {

        $("input[type=text][name='logradouro']").val("");
        $("input[type=text][name='bairro']").val("");
        $("input[type=text][name='complemento']").val("");
        $("input[type=text][name='cidade']").val("");
        $("input[type=text][name='uf']").val("");
    }

</script>
</html>
