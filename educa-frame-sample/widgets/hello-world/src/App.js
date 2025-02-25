import React from "react";
import ClassbookRIOSSample from "./components/ClassbookRIOSSample";
import { Toaster } from "react-hot-toast";

export const App = () => {
  return (
    <>
      <Toaster position="top-center" reverseOrder={false} />
      <ClassbookRIOSSample />
    </>
  );
};
