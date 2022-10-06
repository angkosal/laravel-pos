import React, { Component } from "react";
import ReactDOM from 'react-dom/client';
//import axios from "axios";
import Swal from "sweetalert2";
import { sum } from "lodash";
import dateFormat from "dateformat";

function ExistOrders(props) {
    const orders = props.orders;
    const products = props.products;
    return <div>
        <h5>Order Exist</h5>
        {orders.map((order, index) => (
            <div key={index} className='card h-auto'>
                <div className='card-header'>
                    {order.is_sandbox_order ? <i className="fa-solid fa-flask"></i> : ''} Order # {dateFormat(order.created_at, 'yyyy-mm-dd hh:MMtt')}
                </div>
                <div className="card-body p-1">
                    <table className="table table-striped">
                        <thead>
                        <tr>
                            <th>Product</th>
                            <th>Option</th>
                            <th width="10%">Notes</th>
                            <th>Price({window.APP.currency_symbol})</th>
                        </tr>
                        </thead>
                        <tbody>
                        {order.order_details.map((detail, index) => {
                            let productFound = products.find(product1 => {return product1.id === detail.product_id});
                            return <tr key={index}>
                                <td>{productFound.name}</td>
                                <td>
                                    {detail.product_options.map((option, index) => {
                                        let optionFound = productFound.product_options.find(opt => {return opt.id === parseInt(Object.keys(option)[0])});
                                        let detailFound = optionFound.option_details.find(detail => {return detail.id === option[Object.keys(option)[0]]});
                                        return <div key={index}>
                                            <p>{optionFound.name}: {detailFound.name}</p>
                                        </div>;
                                    })}
                                </td>
                                <td>{detail.notes}</td>
                                <td>{detail.price}</td>
                            </tr>;
                        })}
                        </tbody>
                    </table>
                </div>
            </div>
            )
        )}
    </div>;

}

function NewOrder(props) {
    return <h5>Order not Exist</h5>;
}

function Orders(props) {
    const orders = props.orders;
    const products = props.products;
    const isOrderExist = props.isOrderExist;
    if(isOrderExist){
        return <ExistOrders orders={orders} products={products} />;
    }

    return <NewOrder />;
}

class Cart extends Component {
    constructor(props) {
        super(props);
        this.state = {
            cart: [],
            allProducts: [],
            products: [],
            customers: [],
            barcode: "",
            search: "",
            customer_id: "",
            student_number: "",
            orders: [],
            isOrderExist: false,
            isStudentExist: false,
        };

        this.loadCart = this.loadCart.bind(this);
        this.handleOnChangeBarcode = this.handleOnChangeBarcode.bind(this);
        this.handleScanBarcode = this.handleScanBarcode.bind(this);
        this.handleChangeQty = this.handleChangeQty.bind(this);
        this.handleEmptyCart = this.handleEmptyCart.bind(this);

        this.handleChangeSearch = this.handleChangeSearch.bind(this);
        this.handleSeach = this.handleSeach.bind(this);
        this.setCustomerId = this.setCustomerId.bind(this);
        this.handleClickSubmit = this.handleClickSubmit.bind(this)

        this.loadProducts = this.loadProducts.bind(this);

        this.handleOnChangeStudentNumber = this.handleOnChangeStudentNumber.bind(this);
        this.handleGetStudentOrder = this.handleGetStudentOrder.bind(this);

    }

    componentDidMount() {
        // load user cart
        this.loadCart();
        this.loadProducts();
        this.loadCustomers();
    }

    loadCustomers() {
        axios.get(`/admin/customers`).then(res => {
            const customers = res.data;
            this.setState({ customers });
        });
    }

    loadProducts(search = "") {
        const query = !!search ? `${search}` : "";
        window.axios.get(base_url + '/cart/products?user_id=' + user_id + '&search=' + query)
            .then(response => {
                if(!search){
                    const allProducts = response.data.products;
                    const products = response.data.products;

                    this.setState({ allProducts, products });
                } else {
                    const products = response.data.products;

                    this.setState({ products });
                }
        });
    }

    handleOnChangeStudentNumber(event) {
        const student_number = event.target.value;
        const orders = [];
        console.log(student_number);
        this.setState({ student_number, orders, isOrderExist: false, isStudentExist: false });
    }

    handleGetStudentOrder(event) {
        event.preventDefault();
        const { student_number } = this.state;
        if(!!student_number){
            axios.get(base_url + "/cart/get_student_orders?user_id=" + user_id + '&student_number=' + student_number)
                .then(response => {
                    console.log(response);
                    const orders = response.data.orders;
                    this.setState({ orders, isOrderExist: orders.length > 0, isStudentExist: true });
                })
                .catch(error => {
                    console.log(error);
                    window.SwalWithBootstrap.fire({
                        title: 'Error',
                        html: error.response.data.message,
                        icon: 'error',
                    });
                });
        }
    }

    handleOnChangeBarcode(event) {
        const barcode = event.target.value;
        console.log(barcode);
        this.setState({ barcode });
    }

    loadCart() {
        axios.get("/admin/cart").then(res => {
            const cart = res.data;
            this.setState({ cart });
        });
    }

    handleScanBarcode(event) {
        event.preventDefault();
        const { barcode } = this.state;
        if (!!barcode) {
            axios
                .post("/admin/cart", { barcode })
                .then(res => {
                    this.loadCart();
                    this.setState({ barcode: "" });
                })
                .catch(err => {
                    Swal.fire("Error!", err.response.data.message, "error");
                });
        }
    }

    handleChangeQty(product_id, qty) {
        const cart = this.state.cart.map(c => {
            if (c.id === product_id) {
                c.pivot.quantity = qty;
            }
            return c;
        });

        this.setState({ cart });

        axios
            .post("/admin/cart/change-qty", { product_id, quantity: qty })
            .then(res => { })
            .catch(err => {
                Swal.fire("Error!", err.response.data.message, "error");
            });
    }

    getTotal(cart) {
        const total = cart.map(c => c.pivot.quantity * c.price);
        return sum(total).toFixed(2);
    }

    handleClickDelete(product_id) {
        axios
            .post("/admin/cart/delete", { product_id, _method: "DELETE" })
            .then(res => {
                const cart = this.state.cart.filter(c => c.id !== product_id);
                this.setState({ cart });
            });
    }

    handleEmptyCart() {
        axios.post("/admin/cart/empty", { _method: "DELETE" }).then(res => {
            this.setState({ cart: [] });
        });
    }

    handleChangeSearch(event) {
        const search = event.target.value;
        this.setState({ search });
    }

    handleSeach(event) {
        if (event.keyCode === 13) {
            this.loadProducts(event.target.value);
        }
    }

    addProductToCart(barcode) {

        if(!this.state.isOrderExist){

            if(!this.state.isStudentExist){
                window.SwalWithBootstrap.fire({
                    title: 'Warning',
                    text: 'Please enter a valid student number first.',
                    icon: 'warning',
                });
            }

        }

        // let product = this.state.products.find(p => p.barcode === barcode);
        // if (!!product) {
        //     // if product is already in cart
        //     let cart = this.state.cart.find(c => c.id === product.id);
        //     if (!!cart) {
        //         // update quantity
        //         this.setState({
        //             cart: this.state.cart.map(c => {
        //                 if (c.id === product.id && product.quantity > c.pivot.quantity) {
        //                     c.pivot.quantity = c.pivot.quantity + 1;
        //                 }
        //                 return c;
        //             })
        //         });
        //     } else {
        //         if (product.quantity > 0) {
        //             product = {
        //                 ...product,
        //                 pivot: {
        //                     quantity: 1,
        //                     product_id: product.id,
        //                     user_id: 1
        //                 }
        //             };
        //
        //             this.setState({ cart: [...this.state.cart, product] });
        //         }
        //     }
        //
        //     axios
        //         .post("/admin/cart", { barcode })
        //         .then(res => {
        //             // this.loadCart();
        //             console.log(res);
        //         })
        //         .catch(err => {
        //             Swal.fire("Error!", err.response.data.message, "error");
        //         });
        // }
    }

    setCustomerId(event) {
        this.setState({ customer_id: event.target.value });
    }

    handleClickSubmit() {
        Swal.fire({
            title: 'Received Amount',
            input: 'text',
            inputValue: this.getTotal(this.state.cart),
            showCancelButton: true,
            confirmButtonText: 'Send',
            showLoaderOnConfirm: true,
            preConfirm: (amount) => {
                return axios.post('/admin/orders', { customer_id: this.state.customer_id, amount }).then(res => {
                    this.loadCart();
                    return res.data;
                }).catch(err => {
                    Swal.showValidationMessage(err.response.data.message)
                })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.value) {
                //
            }
        })

    }

    render() {
        const { cart, products, customers, barcode, student_number, orders } = this.state;
        return (
            <div className="container-fluid">
                <div className="row">
                    <div className="col-lg-6">
                        <div className="row mb-2">
                            <div className="col">
                                <form onSubmit={this.handleGetStudentOrder}>
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="Student Number"
                                        value={student_number}
                                        onChange={this.handleOnChangeStudentNumber}
                                    />
                                </form>
                            </div>
                        </div>
                        <div className="row mb-2">
                            <div className="col">
                                <form onSubmit={this.handleScanBarcode}>
                                    <input
                                        type="text"
                                        className="form-control"
                                        placeholder="Barcode"
                                        value={barcode}
                                        onChange={this.handleOnChangeBarcode}
                                        disabled={(this.state.isOrderExist) ? 'disabled' : ''}
                                    />
                                </form>
                            </div>
                            {/*<div className="col">*/}
                            {/*    <select*/}
                            {/*        className="form-control"*/}
                            {/*        onChange={this.setCustomerId}*/}
                            {/*    >*/}
                            {/*        <option value="">Walking Customer</option>*/}
                            {/*        {customers.map(cus => (*/}
                            {/*            <option*/}
                            {/*                key={cus.id}*/}
                            {/*                value={cus.id}*/}
                            {/*            >{`${cus.first_name} ${cus.last_name}`}</option>*/}
                            {/*        ))}*/}
                            {/*    </select>*/}
                            {/*</div>*/}
                        </div>
                        <div className="user-cart">
                            <div className="card">
                                <div className="card-body overflow-auto">
                                    <Orders isOrderExist={orders.length !== 0} products={products} orders={orders} />
                                </div>
                                {/*<table className="table table-striped">*/}
                                {/*    <thead>*/}
                                {/*    <tr>*/}
                                {/*        <th>Product Name</th>*/}
                                {/*        <th>Quantity</th>*/}
                                {/*        <th className="text-right">Price</th>*/}
                                {/*    </tr>*/}
                                {/*    </thead>*/}
                                {/*    <tbody>*/}
                                {/*    {cart.map(c => (*/}
                                {/*        <tr key={c.id}>*/}
                                {/*            <td>{c.name}</td>*/}
                                {/*            <td>*/}
                                {/*                <input*/}
                                {/*                    type="text"*/}
                                {/*                    className="form-control form-control-sm qty"*/}
                                {/*                    value={c.pivot.quantity}*/}
                                {/*                    onChange={event =>*/}
                                {/*                        this.handleChangeQty(*/}
                                {/*                            c.id,*/}
                                {/*                            event.target.value*/}
                                {/*                        )*/}
                                {/*                    }*/}
                                {/*                />*/}
                                {/*                <button*/}
                                {/*                    className="btn btn-danger btn-sm"*/}
                                {/*                    onClick={() =>*/}
                                {/*                        this.handleClickDelete(*/}
                                {/*                            c.id*/}
                                {/*                        )*/}
                                {/*                    }*/}
                                {/*                >*/}
                                {/*                    <i className="fas fa-trash"></i>*/}
                                {/*                </button>*/}
                                {/*            </td>*/}
                                {/*            <td className="text-right">*/}
                                {/*                {window.APP.currency_symbol}{" "}*/}
                                {/*                {(*/}
                                {/*                    c.price * c.pivot.quantity*/}
                                {/*                ).toFixed(2)}*/}
                                {/*            </td>*/}
                                {/*        </tr>*/}
                                {/*    ))}*/}
                                {/*    </tbody>*/}
                                {/*</table>*/}
                            </div>
                        </div>

                        <div className="row">
                            <div className="col">Total:</div>
                            <div className="col text-right">
                                {window.APP.currency_symbol} {this.getTotal(cart)}
                            </div>
                        </div>
                        <div className="row">
                            <div className="col">
                                <button
                                    type="button"
                                    className="btn btn-danger btn-block"
                                    onClick={this.handleEmptyCart}
                                    disabled={!cart.length}
                                >
                                    Cancel
                                </button>
                            </div>
                            <div className="col">
                                <button
                                    type="button"
                                    className="btn btn-primary btn-block"
                                    disabled={!cart.length}
                                    onClick={this.handleClickSubmit}
                                >
                                    Submit
                                </button>
                            </div>
                        </div>
                    </div>
                    <div className="col-lg-6">
                        <div className="mb-2">
                            <input
                                type="text"
                                className="form-control"
                                placeholder="Search Product..."
                                onChange={this.handleChangeSearch}
                                onKeyDown={this.handleSeach}
                                disabled={(this.state.isOrderExist ? 'disabled' : '')}
                            />
                        </div>
                        <div className="order-product">
                            {products.map(p => (
                                <div
                                    onClick={() => this.addProductToCart(p.barcode)}
                                    key={p.id}
                                    className="item"
                                >
                                    <img src={p.media_path !== null ? main_server_url + '/' + p.media_path : main_server_url + '/storage/defaults/product.png'} alt="" />
                                    <h5>{p.name}</h5>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default Cart;

if (document.getElementById("cart")) {
    //ReactDOM.render(<Cart />, document.getElementById("cart"));

    const root = ReactDOM.createRoot(document.getElementById("cart"));
    root.render(<Cart />);
}
