@extends('layouts.app')

@section('content')
    <style>
        #busca::placeholder {
            color:white;
        }
    </style>

    <input type="hidden" id="rota_store" value="{{ route('profile.store') }}">
    <input type="hidden" id="rota_getRoles" value="{{ route('profile.getRoles') }}">
    {{-- <input type="hidden" id="rota_edit" value="{{ route('profile.edit', ['id' => 11]) }}"> --}}
    <input type="hidden" id="rota_update" value="{{ route('profile.update') }}">

    {{-- <a class="btn btn-success">Novo</a> --}}
    <div class="row justify-content-between">
        <div class="btn-group dropend mb-3 col-lg-2">
            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                Novo
            </button>
            <ul class="dropdown-menu bg-success text-light p-2">
                <li>
                    <span class="text-decoration-none text-light" data-bs-toggle="modal" data-bs-target="#modal" style="cursor: pointer" onclick="novoPerfil()">
                        Perfil de acesso
                    </span>
                </li>
            </ul>
        </div>

        <div class="col-lg-4">
            <input type="text" id="busca" class="form-control bg-transparent text-light" placeholder="Busca" onkeyup="getRoles(document.getElementById('rota_getRoles').value)">
        </div>
    </div>

    <table class="table table-hover table-dark" id="roles">
        <thead>
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>Criado em</th>
                <th class="text-center">Ações</th>
            </tr>
        </thead>

        <tbody>
            {{-- JS --}}
        </tbody>
    </table>

    {{-- COLOQUEI CLASSE D-NONE PRA FAZER A PAGINAÇÃO COM OS NUMEROS E SUSPENDER ESSA POR ENQUANTO --}}
    <button id="previous" class="d-none">Anterior</button>
    <button id="next" class="d-none">Próxima</button>

    <div class="row d-flex">

        <div class="col">
            <div id="aguarde" class="spinner-border text-success d-none" role="status">
            </div>
        </div>


        <div id="paginacao" class="text-end col">
            {{-- JS --}}
        </div>
    </div>


  <!-- Modal -->
    <div class="modal fade" id="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" data-bs-dismiss="modal" id="salvar" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    let url_dinamica_busca_atual = null
    getRoles(document.getElementById('rota_getRoles').value)

    async function getRoles(url) {

        document.querySelector('#aguarde').classList.remove('d-none')

        let busca = document.getElementById('busca').value

        const req = await fetch(url, {
            method: 'post',
            headers: {
                'x-csrf-token': document.querySelector('meta[name="csrf-token"]').content,
                'content-type': 'application/json'
            },
            body: JSON.stringify({
                busca: busca
            })
        })

        const res = await req.json()

        exibirRoles(res.data)

        document.querySelector('#aguarde').classList.add('d-none')

        /*
        if (res.next_page_url) {
            document.getElementById('next').style.display = '';
            // Atualizar o botão com a URL da próxima página
            document.getElementById('next').setAttribute('data-next_page_url', res.next_page_url);
        } else {
            // Se não houver mais páginas, ocultar o botão
            document.getElementById('next').style.display = 'none';
        }

        if (res.prev_page_url) {
            document.getElementById('previous').style.display = '';
            // Atualizar o botão com a URL da próxima página
            document.getElementById('previous').setAttribute('data-prev_page_url', res.prev_page_url);

        } else {
            // Se não houver mais páginas, ocultar o botão
            document.getElementById('previous').style.display = 'none';
        }
        */

        // Construindo a paginação
        document.getElementById('paginacao').innerHTML = ''
        res.links.forEach((link, index) => {

            let classe
            let num_pagina = link.label

            // para colorir o botão do número da página em que a pessoa estiver
            if (num_pagina == res.current_page) {
                classe = 'btn-success'

            } else if (
                // ocultando alguns botões pra não ficaram muitos aparecendo. Tanto faz o número
                num_pagina <= res.current_page - 3 ||
                num_pagina >= res.current_page + 5 ||
                !link.url

            ) {
                classe = 'd-none'
            }

            // Trocando o 'Previous' que vem no botão por um ícone
            if (num_pagina.indexOf('Prev') != -1) {
                num_pagina = '<i class="bi bi-caret-left"></i>'

            // Trocando o 'Next' que vem no botão por um ícone
            } else if (num_pagina.indexOf('Next') != -1) {
                num_pagina = '<i class="bi bi-caret-right"></i>'
            }

            document.getElementById('paginacao').innerHTML += `
                <button class="btn border border-dark py-1 px-2 ${classe}" onclick="getRoles('${link.url}')">${num_pagina}</button>
            `

            // Se na url atual não estiver definida nenhuma página com ?page=2 por exemplo, define a url dinamica atual com a página atual
            if (url.indexOf('page') == -1) {
                url_dinamica_busca_atual = `${url}?page=${res.current_page}`

            } else {
                url_dinamica_busca_atual = url
            }
        })
    }

     // preenchendo a tabela
     function exibirRoles(dados){
        let roles = document.getElementById('roles')
        let tbody = roles.querySelector('tbody')

        tbody.innerHTML = ''
        dados.forEach(item => {

            let url_edit = `${window.location.origin}/perfisDeAcesso/editar/${item.id}`;

            tbody.innerHTML += `
                <tr>
                    <td>${item.id}</td>
                    <td>${item.name}</td>
                    <td>${item.created_at}</td>
                    <td class="d-flex justify-content-evenly">
                        <a href="${url_edit}">
                            <i class="bi bi-pencil-fill text-warning"></i>
                        </a>
                        <i class="bi bi-trash-fill text-danger"></i>
                    </td>
                </tr>
            `
        })
    }

    // configura o modal para cadastro de novo perfil de acesso
    function novoPerfil(){
        let modal_body = document.querySelector('#modal .modal-body')
        let modal_footer = document.querySelector('#modal .modal-footer')

        modal_body.innerHTML = `
            <div class="row mb-3">
                <div class="col">
                    <input type="text" id="role" class="form-control" placeholder="Novo perfil de acesso">
                </div>
            </div>
        `

        modal_footer.querySelector('#salvar').onclick = store;
    }


    // Requisição para salvar
    async function store(){
        let url = document.getElementById('rota_store').value
        let token = document.querySelector('meta[name="csrf-token"]').content
        let role = document.getElementById('role').value

        const req  = await fetch(url, {
            method: 'post',
            headers: {
                'x-csrf-token': token,
                'content-type': 'application/json'
            },
            body: JSON.stringify({
                role: role
            })
        })

        const res = await req.json()

        getRoles(url_dinamica_busca_atual)
    }

    // async function editarPerfil(id){
    //     url = document.getElementById('rota_editarPerfil').value
    //     let modal_body = document.querySelector('#modal .modal-body')
    //     let modal_footer = document.querySelector('#modal .modal-footer')

    //     modal_body.innerHTML = `
    //         <div class="row mb-3">
    //             <div class="col">
    //                 <input type="text" id="role" class="form-control" placeholder="Editar perfil de acesso">
    //             </div>
    //         </div>
    //     `

    //     let name = document.getElementById('role').value

    //     const req = await fetch(url, {
    //         method: 'post',
    //         headers: {
    //             'x-csrf-token': document.querySelector('meta[name="csrf-token"]').content,
    //             'content-type': 'application/json'
    //         },
    //         body: JSON.stringify({
    //             id: id,
    //             name: name
    //         })
    //     })

    //     const res = await req.json()

    //     document.getElementById('role').value = res.role.name

    //     let botao_salvar = modal_footer.querySelector('#salvar')
    //     botao_salvar.onclick = update;
    //     botao_salvar.setAttribute('data-id', id)
    // }

    // async function update(){
    //     let url = document.getElementById('rota_update').value
    //     let id = event.target.dataset.id
    //     let name = document.getElementById('role').value

    //     const req = await fetch(url, {
    //         method: 'post',
    //         headers: {
    //             'x-csrf-token': document.querySelector('meta[name="csrf-token"]').content,
    //             'content-type': 'application/json'
    //         },
    //         body: JSON.stringify({
    //             id: id,
    //             name: name
    //         })
    //     })

    //     const res = await req.json()

    //     console.log(res);

    //     getRoles(url_dinamica_busca_atual)
    // }

    // Fazendo a busca ao avançar ou retroceder a página
    // document.getElementById('next').addEventListener('click', function(){
    //     let url = this.dataset.next_page_url
    //     getRoles(url)
    // })

    // document.getElementById('previous').addEventListener('click', function(){
    //     let url = this.dataset.prev_page_url
    //     getRoles(url)
    // })
</script>
@endsection
