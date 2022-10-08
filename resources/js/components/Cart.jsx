import React, { Component } from "react";
import ReactDOM from 'react-dom/client';
//import axios from "axios";
import Swal from "sweetalert2";
import { sum } from "lodash";
import dateFormat from "dateformat";
import { data } from "jquery";

function ExistOrders(props) {
    const orders = props.orders;
    const products = props.products;
    return <div>
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
                                <th style={{ width: '10%' }}>Notes</th>
                                <th>Price({window.APP.currency_symbol})</th>
                            </tr>
                        </thead>
                        <tbody>
                            {order.order_details.map((detail, index) => {
                                let productFound = products.find(product1 => { return product1.id === detail.product_id });
                                return <tr key={index}>
                                    <td>{productFound.name}</td>
                                    <td>
                                        {detail.product_options.map((option, index) => {
                                            let key = Object.keys(option)[0];
                                            let optionFound = productFound.product_options.find(opt => { return opt.id === parseInt(key) });
                                            let detailFound = optionFound.option_details.find(detail => { return detail.id == option[key.toString()] });
                                            console.log(optionFound);
                                            console.log(detailFound);
                                            return <div key={index}>
                                                <p className="mb-0">{optionFound.name}: {detailFound.name}</p>
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
    const products = props.products;
    const isStudentExist = props.isStudentExist;
    const newOrder = props.newOrder;
    if (isStudentExist) {
        return <div>
            <div className="card h-auto">
                <div className="card-header">
                    New Order
                </div>

                <div className="card-body p-1">
                    <table className="table table-stripped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Option</th>
                                <th style={{ width: '10%' }}>Notes</th>
                                <th>Price({window.APP.currency_symbol})</th>
                            </tr>
                        </thead>
                        <tbody>
                            {newOrder.map((order, index) => {
                                let productFound = products.find(product => { return product.id === order.product_id });
                                return <tr key={index}>
                                    <td>{productFound.name}</td>
                                    <td>
                                        {order.product_options.map((option, index) => {
                                            let key = parseInt(Object.keys(option)[0]);
                                            let optionFound = productFound.product_options.find(opt => { return opt.id === key });
                                            let detailFound = optionFound.option_details.find(detail => { return detail.id == option[key.toString()] });
                                            return <div key={index}>
                                                <p className="mb-0">{optionFound.name}: {detailFound.name}</p>
                                            </div>;
                                        })}
                                    </td>
                                    <td>{order.notes}</td>
                                    <td>{order.price}</td>
                                </tr>;
                            })}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>;
    }
    return <div></div>;
}

function Orders(props) {
    const orders = props.orders;
    const newOrder = props.newOrder;
    const products = props.products;
    const isOrderExist = props.isOrderExist;
    const isStudentExist = props.isStudentExist;
    if (isOrderExist) {
        return <ExistOrders orders={orders} products={products} />;
    }

    return <NewOrder isStudentExist={isStudentExist} newOrder={newOrder} products={products} />;
}

class Cart extends Component {

    static optionTemplate = '<div class="container"><div class="row row-cols-auto"><h5 class="col">option_name</h5></div><div class="row row-cols-auto mb-2">option_field</div></div>';
    static detailTemplate = '<div class="col form-check form-check-inline"><input class="form-check-input" type="radio" id="id" name="name" value="value" checked><label class="form-check-label" for="">detail_name</label></div>';
    static noteTemplate = '<div class="container"><div class="row"><h5 class="col-2">Note</h5><textarea class="form-control col" name="note" id="note" cols="10" rows="2"></textarea></div></div>';

    constructor(props) {
        super(props);
        this.state = {
            cart: [],
            allProducts: [],
            products: [],
            barcode: "",
            search: "",
            student_number: "",
            student: {},
            orders: [],
            newOrder: [],
            total: 0,
            isOrderExist: false,
            isStudentExist: false,
        };

        this.handleOnChangeBarcode = this.handleOnChangeBarcode.bind(this);
        this.handleScanBarcode = this.handleScanBarcode.bind(this);

        this.handleChangeSearch = this.handleChangeSearch.bind(this);
        this.handleSeach = this.handleSeach.bind(this);
        this.handleClickSubmit = this.handleClickSubmit.bind(this)

        this.loadProducts = this.loadProducts.bind(this);

        this.handleOnChangeStudentNumber = this.handleOnChangeStudentNumber.bind(this);
        this.handleGetStudentOrder = this.handleGetStudentOrder.bind(this);
        this.handleProductClicked = this.handleProductClicked.bind(this);
        this.handleEmptyOrder = this.handleEmptyOrder.bind(this);

    }

    componentDidMount() {
        this.loadProducts();
    }

    loadProducts(search = "") {
        const query = !!search ? `${search}` : "";
        window.axios.get(base_url + '/cart/products?search=' + query)
            .then(response => {
                if (!search) {
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
        this.setState({ student_number, student: {}, orders, isOrderExist: false, isStudentExist: false, newOrder: [] });
    }

    handleGetStudentOrder(event) {
        event.preventDefault();
        const { student_number } = this.state;
        if (!!student_number) {
            axios.get(base_url + "/cart/get_student_orders?student_number=" + student_number)
                .then(response => {
                    const orders = response.data.orders;
                    const student = response.data.student;
                    this.setState({ orders, student, isOrderExist: orders.length > 0, isStudentExist: true });
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

    handleProductClicked(id) {

        if (this.state.isStudentExist && !this.state.isOrderExist) {
            const productFound = this.state.products.find(product => { return product.id === id });
            let htmlResult = '';
            for (let i = 0; i < productFound.product_options.length; i++) {
                htmlResult = htmlResult + Cart.optionTemplate.replace('option_name', productFound.product_options[i].name);
                let temp = '';
                for (let j = 0; j < productFound.product_options[i].option_details.length; j++) {
                    let detailHtml = Cart.detailTemplate;
                    detailHtml = detailHtml.replace('id="id"', 'id="option' + productFound.product_options[i].id + '"');
                    detailHtml = detailHtml.replace('name="name"', 'name="' + productFound.product_options[i].id + '"');
                    detailHtml = detailHtml.replace('value="value"', 'value="' + productFound.product_options[i].option_details[j].id + '"');
                    detailHtml = detailHtml.replace('detail_name', productFound.product_options[i].option_details[j].name + ' +' + window.APP.currency_symbol + productFound.product_options[i].option_details[j].extra_price);

                    if (productFound.product_options[i].option_details[j].name !== 'None') {
                        detailHtml = detailHtml.replace('checked', '');
                    }

                    temp = temp + detailHtml;
                }

                htmlResult = htmlResult.replace('option_field', temp);
            }

            let html = '<p>' + productFound.description + '</p><p>' + window.APP.currency_symbol + productFound.price + '</p>' + htmlResult + Cart.noteTemplate;

            window.SwalWithBootstrap.fire({
                title: productFound.name,
                imageWidth: 300,
                imageHeight: 200,
                imageUrl: productFound.media_path === null ? main_server_url + '/storage/defaults/product.png' : main_server_url + '/' + productFound.media_path,
                showCancelButton: true,
                reverseButtons: true,
                html: html,
                preConfirm: () => {
                    let note = SwalWithBootstrap.getPopup().querySelector('#note').value;
                    let data = {
                        note: note,
                        price: parseFloat(productFound.price).toFixed(2),
                        options: [],
                    }

                    for (let i = 0; i < productFound.product_options.length; i++) {
                        let input = SwalWithBootstrap.getPopup().querySelector('#option' + productFound.product_options[i].id + ':checked').value;
                        data.options.push({
                            [productFound.product_options[i].id]: input,
                        });
                        let details = productFound.product_options[i].option_details;
                        let detailFound = details.find(detail => { return detail.id == input });
                        data.price = (parseFloat(data.price) + parseFloat(detailFound.extra_price)).toFixed(2);
                    }

                    return data;
                },
            }).then((swalResult) => {
                let value = swalResult.value;

                let orderDetail = {
                    product_id: productFound.id,
                    product_options: value.options,
                    price: value.price,
                    notes: value.note,
                }

                let orders = this.state.newOrder;
                orders.push(orderDetail);

                let total = (parseFloat(this.state.total) + parseFloat(value.price)).toFixed(2);

                this.setState({ newOrder: orders, total: total });
            });

        } else {
            if (this.state.isOrderExist) {
                window.SwalWithBootstrap.fire({
                    title: 'Warning',
                    text: 'The student has order haven\'t complete.',
                    icon: 'warning',
                });
            } else {
                window.SwalWithBootstrap.fire({
                    title: 'Warning',
                    text: 'Please enter a student number first.',
                    icon: 'warning',
                });
            }
        }

    }

    handleChangeSearch(event) {
        const search = event.target.value;
        this.setState({ search, products: this.state.allProducts });
    }

    handleSeach(event) {
        if (event.keyCode === 13) {
            this.loadProducts(event.target.value);
        }
    }

    handleOnChangeBarcode(event) {
        const barcode = event.target.value;
        this.setState({ barcode, products: this.state.allProducts });
    }

    handleScanBarcode(event) {

        if(event.keyCode === 13){

            const barcode = this.state.barcode;
            if(!!barcode){

                window.axios.get(base_url + '/cart/get_products_barcode?barcode=' + barcode).then(response => {
                    const products = response.data.products;
                    this.setState({products});
                }).catch(error => {
                    console.log(error);
                    window.SwalWithBootstrap.fire({
                        title: 'Error',
                        html: error.response.data.message,
                        icon: 'error',
                    });
                });

            }

        }
    }

    handleEmptyOrder() {
        this.setState({ newOrder: [], orders: [], student_number: '', student: {}, isStudentExist: false, isOrderExist: false });
    }

    handleClickSubmit() {

        if(this.state.isOrderExist){
            let orderIds = [];
            this.state.orders.forEach(order => {
               orderIds.push(order.id);
            });

            window.axios.post(
                base_url + '/cart/store',
                {
                    isNewOrder: !this.state.isOrderExist,
                    student_number: this.state.student_number,
                    order_ids: orderIds,
                })
                .then(response => {
                    window.SwalWithBootstrap.fire({
                        title: 'Success',
                        text: response.data.message,
                        icon: 'success',
                    }).then(() => {
                        this.handleEmptyOrder();
                    });
                })
                .catch(error => {
                    console.log(error);
                    window.SwalWithBootstrap.fire({
                        title: 'Error',
                        text: response.message,
                        icon: 'error',
                    });
                });
        } else {
            console.log(this.state.newOrder);

            window.axios.post(base_url + '/cart/store', {
                isNewOrder: !this.state.isOrderExist,
                student_number: this.state.student_number,
                order_details: this.state.newOrder,
                total_price: this.state.total,
            })
                .then(response => {
                    console.log(response);
                    window.SwalWithBootstrap.fire({
                        title: 'Success',
                        text: response.data.message,
                        icon: 'success',
                    }).then(() => {
                        this.handleEmptyOrder();
                    });
                })
                .catch(error => {
                    console.log(error);
                    window.SwalWithBootstrap.fire({
                        title: 'Error',
                        text: error.message,
                        icon: 'error',
                    });
                });
        }

        /*Swal.fire({
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
        })*/

    }

    render() {
        const { cart, products, barcode, student_number, student, orders, newOrder, total } = this.state;
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
                                <input type="text"
                                    className="form-control"
                                    placeholder="Student Name"
                                    value={(student && Object.keys(student).length === 0 && Object.getPrototypeOf(student) === Object.prototype) ? '' : student.first_name + ' ' + student.last_name}
                                    disabled='disabled'
                                />
                            </div>
                        </div>
                        <div className="user-cart">
                            <div className="card">
                                <div className="card-body overflow-auto">
                                    <Orders isOrderExist={this.state.isOrderExist} isStudentExist={this.state.isStudentExist} products={products} orders={orders} newOrder={newOrder} />
                                </div>
                            </div>
                        </div>

                        <div className="row">
                            <div className="col">Total:</div>
                            <div className="col text-right">
                                {window.APP.currency_symbol} {total}
                            </div>
                        </div>
                        <div className="row mb-4">
                            <div className="col">
                                <button
                                    type="button"
                                    className="btn btn-danger btn-block"
                                    onClick={this.handleEmptyOrder}
                                    disabled={(newOrder.length !== 0 || orders.length !== 0 || this.state.isStudentExist) ? '' : 'disabled'}
                                >
                                    Reset
                                </button>
                            </div>
                            <div className="col">
                                <button
                                    type="button"
                                    className="btn btn-primary btn-block"
                                    disabled={(newOrder.length !== 0 || orders.length !== 0) ? '' : 'disabled'}
                                    onClick={this.handleClickSubmit}
                                >
                                    Submit
                                </button>
                            </div>
                        </div>
                    </div>
                    <div className="col-lg-6 mb-4">
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
                        <div className="mb-2">
                            <input
                                type="text"
                                className="form-control"
                                placeholder="Barcode"
                                onChange={this.handleOnChangeBarcode}
                                onKeyDown={this.handleScanBarcode}
                                disabled={(this.state.isOrderExist) ? 'disabled' : ''}
                            />
                        </div>
                        <div className="order-product">
                            {products.map(product => (
                                <div
                                    onClick={() => this.handleProductClicked(product.id)}
                                    key={product.id}
                                    className="item"
                                >
                                    <img src={product.media_path !== null ? main_server_url + '/' + product.media_path : main_server_url + '/storage/defaults/product.png'} alt="" />
                                    <h5>{product.name}</h5>
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
