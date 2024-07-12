// import { Button, ButtonGroup } from '@chakra-ui/react';

// const Pagination = ({ currentPage, totalPages, setCurrentPage }) => {
//   return (
//     <ButtonGroup>
//       <Button
//         onClick={() => setCurrentPage(prev => Math.max(prev - 1, 1))}
//         isDisabled={currentPage === 1}
//       >
//         Previous
//       </Button>
//       {[...Array(totalPages)].map((_, index) => (
//         <Button
//           key={index}
//           onClick={() => setCurrentPage(index + 1)}
//           isActive={currentPage === index + 1}
//         >
//           {index + 1}
//         </Button>
//       ))}
//       <Button
//         onClick={() => setCurrentPage(prev => Math.min(prev + 1, totalPages))}
//         isDisabled={currentPage === totalPages}
//       >
//         Next
//       </Button>
//     </ButtonGroup>
//   );
// };


// export default Pagination