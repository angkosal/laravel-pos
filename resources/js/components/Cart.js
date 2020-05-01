import React, {Component} from 'react';
import ReactDOM from 'react-dom'
import axios from 'axios'
import Swal from 'sweetalert2'
import {sum} from 'lodash'

class Cart extends Component {

    constructor(props) {
        super(props)
        this.state = {
            cart: [],
            barcode: '',
        };

        this.loadCart = this.loadCart.bind(this);
        this.handleOnChangeBarcode = this.handleOnChangeBarcode.bind(this)
        this.handleScanBarcode = this.handleScanBarcode.bind(this)
    }

    componentDidMount() {
        // load user cart
        this.loadCart();
    }

    handleOnChangeBarcode(event) {
        const barcode = event.target.value;
        console.log(barcode);
        this.setState({barcode})
    }

    loadCart() {
        axios.get('/admin/cart').then(res => {
            const cart = res.data;
            this.setState({cart})
        })
    }

    handleScanBarcode(event) {
        event.preventDefault();
        const {barcode} = this.state;
        if (!!barcode) {
            axios.post('/admin/cart', {barcode}).then(res => {
                this.loadCart();
                this.setState({barcode: ''})
            }).catch(err => {
                Swal.fire(
                    'Error!',
                    err.response.data.message,
                    'error'
                )
            })
        }
    }
    handleChangeQty (event) {
        //
    }

    getTotal(cart) {
        const total = cart.map(c => c.pivot.quantity * c.price);
        return sum(total).toFixed(2);
    }

    render() {
        const {cart, barcode} = this.state;
        return (
            <div className="row">
                <div className="col-md-6 col-lg-4">
                    <div className="row mb-2">
                        <div className="col">
                            <form onSubmit={this.handleScanBarcode}>
                                <input
                                    type="text"
                                    className="form-control"
                                    placeholder="Scan Barcode..."
                                    value={barcode}
                                    onChange={this.handleOnChangeBarcode}
                                />
                            </form>
                        </div>
                        <div className="col">
                            <select name="" id="" className="form-control">
                                <option value="">Walking Customer</option>
                            </select>
                        </div>
                    </div>
                    <div className="user-cart">
                        <div className="card">
                            <table className="table table-striped">
                                <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th className="text-right">Price</th>
                                </tr>
                                </thead>
                                <tbody>
                                {cart.map(c => (
                                    <tr key={c.id}>
                                        <td>{c.name}</td>
                                        <td>
                                            <input type="text" className="form-control form-control-sm qty" value={c.pivot.quantity} onChange={this.handleChangeQty} />
                                            <button className="btn btn-danger btn-sm">
                                                <i className="fas fa-trash"></i>
                                            </button>
                                        </td>
                                        <td className="text-right">$ {(c.price * c.pivot.quantity).toFixed(2)}</td>
                                    </tr>
                                ))}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div className="row">
                        <div className="col">Total:</div>
                        <div className="col text-right">$ {this.getTotal(cart)}</div>
                    </div>
                    <div className="row">
                        <div className="col">
                            <button type="button" className="btn btn-danger btn-block">Cancel</button>
                        </div>
                        <div className="col">
                            <button type="button" className="btn btn-primary btn-block">Submit</button>
                        </div>
                    </div>
                </div>
                <div className="col-md-6 col-lg-8">
                    <div className="mb-2">
                        <input type="text" className="form-control" placeholder="Search Product..."/>
                    </div>
                    <div className="order-product">
                        <div className="item">
                            <img
                                src="http://localhost:8000/storage/products/u00TnlJsFeTuq8ZHJty8vbwpgONrImTn0dUihduu.jpeg"
                                alt=""/>
                            <h5>Coca</h5>
                        </div>
                        <div className="item">
                            <img
                                src="http://localhost:8000/storage/products/u00TnlJsFeTuq8ZHJty8vbwpgONrImTn0dUihduu.jpeg"
                                alt=""/>
                            <h5>Coca</h5>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default Cart;

if (document.getElementById('cart')) {
    ReactDOM.render(<Cart/>, document.getElementById('cart'))
}
