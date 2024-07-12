import React from 'react';
import './App.css';
import CRoutes from './Routes';
import QueryProvider from './QueryProvider';
import { ChakraProvider } from '@chakra-ui/react'
const App = () => {
  return (
    <div className='App'>
      <ChakraProvider>
      <QueryProvider>
        <CRoutes/>
      </QueryProvider>
      </ChakraProvider>
    </div>

  );
}

export default App;
