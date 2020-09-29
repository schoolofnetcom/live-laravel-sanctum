import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';

import api from '../../services/api';

const Login = () => {

  const [usersWithToken, setUsersWithToken] = useState([]);
  const [usersWithoutToken, setUsersWithoutToken] = useState([]);

  /**
   * acessa rota api/users para listagem de usuários sem o token
   * o acesso só é liberado caso o usuário esteja com o cookie válido no navegador
   */ 
  const listUsersWithoutToken = () => {
    api.get('api/users').then((response) => {
      setUsersWithoutToken(response.data);
    })
  }

  /**
   * acessa rota api/users para listagem de usuários com o token
   * desde que o usuário esteja com um token válido o acesso sempre é autorizado
   * se o usuário estiver com o cookie de sessão válido o token é opcional
   */
  const listUsersWithToken = () => {
    const token = 'seu-token-aqui';
    const config = {
        headers: { Authorization: `Bearer ${token}` }
    };
    api.get('api/users', config).then((response) => {
      setUsersWithToken(response.data);
    })
  }

  return (
    <>
      <div className='btn'>
        <Link to='/'>Login</Link>
      </div>

      <h1>Bem vindo ao dashboard!!!</h1>

      <button onClick={listUsersWithoutToken} className='btn'>
        Listar usuários
        <span><small style={{ fontSize: '0.6em', marginTop: '-15px' }}> sem token</small></span>
      </button>

      <ul>
        {
          usersWithoutToken.map(user => (
            <li key={user.id}>{user.name}</li>
          ))
        }
      </ul>

      <br/>

      <button onClick={listUsersWithToken} className='btn'>
        Listar usuários
        <span><small style={{ fontSize: '0.6em', marginTop: '-15px' }}> com token</small></span>
      </button>

      <ul>
        {
          usersWithToken.map(user => (
            <li key={user.id}>{user.name}</li>
          ))
        }
      </ul>
    </>
  )
}
  

export default Login;