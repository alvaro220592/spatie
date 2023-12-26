@extends('layouts.app')

@section('content')
    <input type="hidden" id="rota_getPermissions" value="{{ route('permission.getPermissions') }}">
    <input type="hidden" id="rota_salvar" value="{{ route('permission.update') }}">
    <input type="hidden" id="role_id" value="{{ $role->id }}">

    <h3 class="text-light">Permissões do perfil {{ $role->name }}</h3>

    <div id="container">
        {{-- JS --}}
    </div>

    @can('Visualizar funcionalidade')
        <div class="row">
            <div class="col">
                <button class="btn btn-outline-light" onclick="salvar()">Salvar</button>
            </div>
        </div>
    @endcan



@endsection

@section('scripts')
    <script>
        getPermissions()
        async function getPermissions(){
            url = document.getElementById('rota_getPermissions').value
            let role_id = document.getElementById('role_id').value

            const req = await fetch(url, {
                method: 'post',
                headers: {
                    'x-csrf-token': document.querySelector('meta[name="csrf-token"]').content,
                    'content-type': 'application/json'
                },
                body: JSON.stringify({
                    role_id: role_id
                })
            })

            const res = await req.json()

            let container_div = document.getElementById('container')

            container_div.innerHTML = ''

            res.funcionalidades.forEach(funcionalidade => {


                let permissions = funcionalidade.permissions.map(function(permission) {

                    // Verificação pra marcar o checkbox
                    let checked
                    permission.roles.forEach(role => { // pra cada perfil de acesso dessa permissão, se existir o perfil e se o id dele for igual o id do role vindo do controller, marca
                        if (role && role.id == res.role_id) {
                            checked = 'checked'
                        } else {
                            checked = ''
                        }
                    })

                    // Os ids dos checkboxes foram concatenados pois só com o numero do id, o primeiro checkbox nao mudava ao lcicar no label
                    return `
                        <div class="row">
                            <div class="col">
                                <input type="checkbox" id="${funcionalidade.id}${permission.id}" class="permissao" ${checked} data-permission_id="${permission.id}">
                                <label for="${funcionalidade.id}${permission.id}">
                                    ${permission.name}<br>
                                </label>
                            </div>
                        </div>
                    `
                }).join('')

                container_div.innerHTML += `
                    <div id="${funcionalidade.id}" class="card bg-dark text-light p-3 border border-light mb-3 funcionalidade">
                        <h3>${funcionalidade.name}</h3>
                        ${permissions}
                    </div>
                `
            })
        }

        async function salvar(){
            let url = document.getElementById('rota_salvar').value

            let funcionalidades = document.querySelectorAll('.funcionalidade')
            let role_id = document.getElementById('role_id').value

            let array_permissoes = []
            funcionalidades.forEach(funcionalidade => {
                let permissoes = funcionalidade.querySelectorAll('.permissao')

                permissoes.forEach(permissao => {
                    array_permissoes.push({
                        permissao_id: permissao.dataset.permission_id,
                        funcionalidade_id: funcionalidade.id,
                        checked: permissao.checked ? true : false
                    })
                })
            })

            const req = await fetch(url, {
                method: 'post',
                headers: {
                    'x-csrf-token': document.querySelector('meta[name="csrf-token"]').content,
                    'content-type': 'application/json'
                },
                body: JSON.stringify({
                    role_id: role_id,
                    permissoes: array_permissoes
                })
            })

            const res = await req.json()

            window.location = res.redirect_url
        }
    </script>
@endsection
